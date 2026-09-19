<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /**
     * Enregistre une ouverture d'email via le pixel 1x1.
     *
     * Route : GET /track/open/{token}
     * Le {token} est un UUID opaque — impossible à itérer ou deviner.
     */
    public function open(string $token)
    {
        $emailLog = EmailLog::findByToken($token);

        if ($emailLog) {
            $updates = [];

            if (! $emailLog->opened) {
                $updates['opened'] = true;
            }

            if ($emailLog->status === EmailLog::STATUS_SENT) {
                $updates['status'] = EmailLog::STATUS_DELIVERED;
            }

            if (! empty($updates)) {
                $emailLog->update($updates);
            }

            if ($emailLog->contact) {
                $emailLog->contact->advanceStatusTo(Contact::STATUS_EMAIL_OUVERT);
            }
        }

        // 1x1 transparent GIF — répondre immédiatement même si log introuvable
        $pixel = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');

        return response($pixel, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Enregistre un clic et redirige vers l'URL cible.
     *
     * Route : GET /track/click/{token}?url={encoded_url}
     * Le {token} est un UUID opaque — impossible à itérer ou deviner.
     */
    public function click(Request $request, string $token)
    {
        $destination = $request->query('url', '/');

        // Valider que c'est une URL absolue (évite les redirections malveillantes)
        if (
            ! filter_var($destination, FILTER_VALIDATE_URL)
            || ! in_array(parse_url($destination, PHP_URL_SCHEME), ['http', 'https'])
        ) {
            return redirect(config('app.url'));
        }

        $emailLog = EmailLog::findByToken($token);

        if ($emailLog) {
            $updates = [];

            // Marquer comme cliqué (premier clic uniquement)
            if (! $emailLog->clicked) {
                $updates['clicked']    = true;
                $updates['clicked_at'] = now();
            }

            // Toujours incrémenter le compteur de clics
            $updates['clicked_count'] = DB::raw('clicked_count + 1');

            // Si le mail n'était pas encore ouvert, le marquer comme ouvert aussi
            if (! $emailLog->opened) {
                $updates['opened'] = true;
            }

            // Marquer comme délivré si encore en statut "sent"
            if ($emailLog->status === EmailLog::STATUS_SENT) {
                $updates['status'] = EmailLog::STATUS_DELIVERED;
            }

            $emailLog->update($updates);

            // Avancer le statut prospect
            if ($emailLog->contact) {
                $emailLog->contact->advanceStatusTo(Contact::STATUS_EMAIL_OUVERT);
            }
        }

        return redirect()->away($destination);
    }
}
