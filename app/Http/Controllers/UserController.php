<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with(['roles', 'smtpSetting']);

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->pluck('name');

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Dashboard monitoring for all users activity.
     */
    public function monitoring(Request $request)
    {
        $query = User::query()->with(['roles', 'campaigns', 'importLogs']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        $users = $query->latest()->get();
        $roles = Role::orderBy('name')->pluck('name');

        $totalEmailsSent = 0;
        $totalEmailsOpened = 0;
        $totalEmailsClicked = 0;
        $totalCampaigns = 0;

        $userStatsList = $users->map(function ($user) use (&$totalEmailsSent, &$totalEmailsOpened, &$totalEmailsClicked, &$totalCampaigns) {
            $st = $user->stats;
            $totalEmailsSent += $st['emails_sent'];
            $totalEmailsOpened += $st['emails_opened'];
            $totalEmailsClicked += $st['emails_clicked'];
            $totalCampaigns += $st['total_campaigns'];

            return [
                'user' => $user,
                'stats' => $st,
            ];
        });

        $globalOpenRate = $totalEmailsSent > 0 ? round(($totalEmailsOpened / $totalEmailsSent) * 100, 1) : 0;
        $globalClickRate = $totalEmailsSent > 0 ? round(($totalEmailsClicked / $totalEmailsSent) * 100, 1) : 0;

        $globalStats = [
            'total_users' => $users->count(),
            'total_campaigns' => $totalCampaigns,
            'total_emails_sent' => $totalEmailsSent,
            'total_emails_opened' => $totalEmailsOpened,
            'total_emails_clicked' => $totalEmailsClicked,
            'global_open_rate' => $globalOpenRate,
            'global_click_rate' => $globalClickRate,
        ];

        return view('users.monitoring', compact('userStatsList', 'roles', 'globalStats'));
    }

    /**
     * Detailed monitoring & activity log for an individual user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'importLogs']);
        $stats = $user->stats;

        $campaigns = $user->campaigns()
            ->with(['category', 'importLog'])
            ->withCount([
                'emailLogs as envoyes_count' => function ($q) {
                    $q->whereIn('status', [\App\Models\EmailLog::STATUS_SENT, \App\Models\EmailLog::STATUS_DELIVERED]);
                },
                'emailLogs as delivered_count' => function ($q) {
                    $q->where('status', \App\Models\EmailLog::STATUS_DELIVERED);
                },
                'emailLogs as ouverts_count' => function ($q) {
                    $q->where('opened', true);
                },
                'emailLogs as clics_count' => function ($q) {
                    $q->where('clicked', true);
                },
                'emailLogs as erreurs_count' => function ($q) {
                    $q->whereIn('status', [\App\Models\EmailLog::STATUS_FAILED, \App\Models\EmailLog::STATUS_BOUNCED, \App\Models\EmailLog::STATUS_INVALID]);
                }
            ])
            ->latest()
            ->paginate(10);

        $imports = $user->importLogs()->latest()->take(10)->get();

        return view('users.show', compact('user', 'stats', 'campaigns', 'imports'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'smtp_email' => ['nullable', 'email', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_sender_name' => ['nullable', 'string', 'max:255'],
            'smtp_rate_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est deja utilise.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caracteres.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles'], $validated['smtp_email'], $validated['smtp_password'], $validated['smtp_sender_name'], $validated['smtp_rate_limit']);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        if ($request->filled('smtp_email')) {
            $smtpData = [
                'user_id' => $user->id,
                'provider' => 'OVHcloud SMTP',
                'driver' => 'smtp',
                'host' => 'ssl0.ovh.net',
                'port' => 587,
                'encryption' => 'tls',
                'username' => trim($request->input('smtp_email')),
                'sender_email' => trim($request->input('smtp_email')),
                'sender_name' => $request->filled('smtp_sender_name') ? trim($request->input('smtp_sender_name')) : $user->name,
                'reply_to_email' => trim($request->input('smtp_email')),
                'rate_limit' => max(1, (int) $request->input('smtp_rate_limit', 3)),
                'is_active' => true,
            ];

            if ($request->filled('smtp_password')) {
                $smtpData['password'] = $request->input('smtp_password');
            }

            $user->smtpSetting()->updateOrCreate(['user_id' => $user->id], $smtpData);
        }

        $successMsg = "Utilisateur {$user->name} ({$user->email}) créé avec succès.";

        if ($request->input('source') === 'settings' || $request->input('redirect_to') === 'profile') {
            return redirect()->route('profile.edit')->with('success', $successMsg);
        }

        return redirect()->route('users.index')->with('success', $successMsg);
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'smtpSetting']);
        $roles = Role::orderBy('name')->pluck('name');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'smtp_email' => ['nullable', 'email', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_sender_name' => ['nullable', 'string', 'max:255'],
            'smtp_rate_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles'], $validated['smtp_email'], $validated['smtp_password'], $validated['smtp_sender_name'], $validated['smtp_rate_limit']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($roles);

        if ($request->filled('smtp_email')) {
            $smtpData = [
                'user_id' => $user->id,
                'provider' => 'OVHcloud SMTP',
                'driver' => 'smtp',
                'host' => 'ssl0.ovh.net',
                'port' => 587,
                'encryption' => 'tls',
                'username' => trim($request->input('smtp_email')),
                'sender_email' => trim($request->input('smtp_email')),
                'sender_name' => $request->filled('smtp_sender_name') ? trim($request->input('smtp_sender_name')) : $user->name,
                'reply_to_email' => trim($request->input('smtp_email')),
                'rate_limit' => max(1, (int) $request->input('smtp_rate_limit', 3)),
                'is_active' => true,
            ];

            if ($request->filled('smtp_password')) {
                $smtpData['password'] = $request->input('smtp_password');
            }

            $user->smtpSetting()->updateOrCreate(['user_id' => $user->id], $smtpData);
        } elseif ($user->smtpSetting) {
            $user->smtpSetting()->delete();
        }

        $successMsg = "Utilisateur {$user->name} mis à jour avec succès.";

        if ($request->input('source') === 'settings' || $request->input('redirect_to') === 'profile') {
            return redirect()->route('profile.edit')->with('success', $successMsg);
        }

        return redirect()->route('users.index')->with('success', $successMsg);
    }

    /**
     * Test SMTP connection for given email & password in real time via AJAX.
     */
    public function testSmtp(Request $request, ?User $user = null)
    {
        $email = trim((string) $request->input('smtp_email'));
        $password = (string) $request->input('smtp_password');

        if (! $email) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez saisir une adresse email OVH valide.',
            ]);
        }

        if (empty($password) && $user && $user->smtpSetting) {
            $password = (string) $user->smtpSetting->password;
        }

        if (empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez renseigner le mot de passe de la boîte OVH pour effectuer le test.',
            ]);
        }

        try {
            // Port 587 STARTTLS
            $transport = new EsmtpTransport('ssl0.ovh.net', 587, false);
            $transport->setUsername($email);
            $transport->setPassword($password);

            $transport->start();
            $transport->stop();

            return response()->json([
                'success' => true,
                'message' => "Connexion OVH réussie ! La boîte {$email} répond parfaitement et est prête à envoyer.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de connexion OVH : ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        $user->delete();

        $successMsg = "Utilisateur {$name} supprimé avec succès.";

        if ($request->input('source') === 'settings' || $request->input('redirect_to') === 'profile') {
            return redirect()->route('profile.edit')->with('success', $successMsg);
        }

        return redirect()->route('users.index')->with('success', $successMsg);
    }
}
