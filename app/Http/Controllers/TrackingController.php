<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function open($log_id)
    {
        $emailLog = EmailLog::find($log_id);
        if ($emailLog && !$emailLog->opened) {
            $emailLog->update(['opened' => true]);
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
