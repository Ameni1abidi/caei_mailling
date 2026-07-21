<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmtpSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider'       => 'required|string|max:50',
            'driver'         => 'required|string|in:smtp,ses,mailgun,log',
            'host'           => 'required_if:driver,smtp|nullable|string|max:255',
            'port'           => 'required_if:driver,smtp|nullable|integer|between:1,65535',
            'username'       => 'nullable|string|max:255',
            'password'       => 'nullable|string|max:255',
            'encryption'     => 'nullable|in:tls,ssl',
            'api_key'        => 'nullable|string|max:500',
            'sender_name'    => 'required|string|max:255',
            'sender_email'   => 'required|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'rate_limit'     => 'nullable|integer|min:1|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required'      => 'Le fournisseur est obligatoire.',
            'driver.required'        => 'Le driver est obligatoire.',
            'driver.in'              => 'Le driver doit être smtp, ses, mailgun ou log.',
            'host.required_if'       => 'Le serveur SMTP est obligatoire pour le driver SMTP.',
            'port.required_if'       => 'Le port est obligatoire pour le driver SMTP.',
            'port.between'           => 'Le port doit être compris entre 1 et 65535.',
            'sender_name.required'   => 'Le nom de l\'expéditeur est obligatoire.',
            'sender_email.required'  => 'L\'email de l\'expéditeur est obligatoire.',
            'sender_email.email'     => 'L\'email de l\'expéditeur doit être une adresse valide.',
            'reply_to_email.email'   => 'L\'email de réponse doit être une adresse valide.',
            'rate_limit.min'         => 'La limite d\'envoi doit être d\'au moins 1 email/minute.',
            'rate_limit.max'         => 'La limite d\'envoi ne peut pas dépasser 10 000 emails/minute.',
        ];
    }
}
