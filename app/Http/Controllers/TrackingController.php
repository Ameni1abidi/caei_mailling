<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function open($log_id)
    {
        $emailLog = EmailLog::with('contact')->find($log_id);
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

        // 1x1 transparent GIF
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
     * URL pattern : /track/click/{log_id}?url={encoded_url}
     */
    public function click(Request $request, $log_id)
    {
        $destination = $request->query('url', '/');

        // Valider que c'est une URL absolue (évite les redirections malveillantes)
        if (! filter_var($destination, FILTER_VALIDATE_URL) || ! in_array(parse_url($destination, PHP_URL_SCHEME), ['http', 'https'])) {
            return redirect()->away('https://' . config('app.url'));
        }

        $emailLog = EmailLog::with('contact')->find($log_id);

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
