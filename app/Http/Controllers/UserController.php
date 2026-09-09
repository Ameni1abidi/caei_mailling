<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

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
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est deja utilise.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caracteres.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        $successMsg = "Utilisateur {$user->name} ({$user->email}) créé avec succès.";

        if ($request->input('source') === 'settings' || $request->input('redirect_to') === 'profile') {
            return redirect()->route('profile.edit')->with('success', $successMsg);
        }

        return redirect()->route('users.index')->with('success', $successMsg);
    }

    public function edit(User $user)
    {
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
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($roles);

        $successMsg = "Utilisateur {$user->name} mis à jour avec succès.";

        if ($request->input('source') === 'settings' || $request->input('redirect_to') === 'profile') {
            return redirect()->route('profile.edit')->with('success', $successMsg);
        }

        return redirect()->route('users.index')->with('success', $successMsg);
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
