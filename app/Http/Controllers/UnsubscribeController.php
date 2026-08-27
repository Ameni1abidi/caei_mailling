<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    /**
     * Désinscrire un contact via le lien dans l'email.
     */
    public function unsubscribe(string $email)
    {
        $contact = Contact::where('email', $email)->first();

        if ($contact && $contact->unsubscribed_at === null) {
            $contact->update([
                'unsubscribed_at' => now(),
            ]);
        }

        return view('emails.unsubscribed');
    }
}
