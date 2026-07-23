<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;

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
}
