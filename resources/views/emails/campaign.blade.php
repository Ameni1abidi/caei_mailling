<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $objetPersonnalise ?? 'CAEI' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px;">
        {!! $contenuPersonnalise !!}

        <hr style="margin-top: 30px; border: none; border-top: 1px solid #eee;">

        <div style="font-size: 11px; color: #999; text-align: center; padding: 15px 0;">
            <p style="margin: 5px 0;">Vous recevez cet email car vous êtes enregistré dans la base de contacts CAEI.</p>
            <p style="margin: 5px 0;">CAEI - Cabinet d'Audit, d'Expertise et d'Ingénierie</p>
            @if(isset($contact) && $contact)
                <p style="margin: 10px 0;">
                    <a href="{{ route('contact.unsubscribe', ['email' => $contact->email]) }}"
                       style="color: #999; text-decoration: underline; font-size: 11px;">
                        Se désinscrire
                    </a>
                </p>
            @endif
        </div>
    </div>

    @if(isset($emailLogId) && $emailLogId)
        <img src="{{ route('track.open', ['log_id' => $emailLogId]) }}" width="1" height="1" style="display:none;" alt="" />
    @endif
</body>
</html>
