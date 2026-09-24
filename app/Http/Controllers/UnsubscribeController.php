<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UnsubscribeController extends Controller
{
    /**
     * Désinscrire un contact via le lien dans l'email.
     * Sécurisé par token opaque (UUID de l'EmailLog) pour éviter la désinscription arbitraire.
     */
    public function unsubscribe(Request $request, string $email)
    {
        // Tentative de vérification par token (nouveaux liens)
        $token   = $request->query('token');
        $contact = null;

        if ($token) {
            // Token = tracking_token de l'EmailLog — lien unique et non-devinable
            $emailLog = EmailLog::where('tracking_token', $token)
                ->whereHas('contact', fn ($q) => $q->where('email', $email))
                ->first();

            if ($emailLog) {
                $contact = $emailLog->contact;
            }
        } else {
            // Fallback pour les anciens liens sans token — rate limiting par IP
            $ip      = $request->ip();
            $cacheKey = 'unsub_' . md5($ip);
            $attempts = Cache::get($cacheKey, 0);

            if ($attempts >= 5) {
                // Trop de tentatives de désinscription depuis cette IP → ignorer silencieusement
                return view('emails.unsubscribed');
            }

            Cache::put($cacheKey, $attempts + 1, now()->addHours(1));
            $contact = Contact::where('email', $email)->first();
        }

        if ($contact && $contact->unsubscribed_at === null) {
            $contact->update(['unsubscribed_at' => now()]);
        }

        return view('emails.unsubscribed');
    }
}
