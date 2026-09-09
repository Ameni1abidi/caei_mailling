<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $objetPersonnalise ?? 'CAEI' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; color: #1e293b;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="left" style="padding: 20px 16px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <!-- Contenu principal fluide naturel -->
                    <tr>
                        <td style="color: #1e293b; font-size: 15px; line-height: 1.65;">
                            {!! $contenuPersonnalise !!}
                        </td>
                    </tr>

                    <!-- Pied de page discret (évite le look newsletter publicitaire) -->
                    <tr>
                        <td style="padding-top: 36px; padding-bottom: 20px; border-top: 1px solid #f1f5f9; color: #94a3b8; font-size: 11px; line-height: 1.5;">
                            <p style="margin: 0 0 4px 0;">
                                CAEI &bull; Cabinet d'Audit, d'Expertise et d'Ingénierie
                            </p>
                            @if(isset($contact) && $contact)
                                <p style="margin: 0;">
                                    <a href="{{ route('contact.unsubscribe', ['email' => $contact->email]) }}"
                                       style="color: #94a3b8; text-decoration: underline;">
                                        Se désinscrire
                                    </a>
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Pixel d'ouverture discret -->
    @if(isset($emailLogId) && $emailLogId)
        <img src="{{ route('track.open', ['log_id' => $emailLogId]) }}" width="1" height="1" border="0" alt="" style="width:1px!important;height:1px!important;border:0!important;margin:0!important;padding:0!important;outline:none!important;" />
    @endif
</body>
</html>
