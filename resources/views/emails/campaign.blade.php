<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $objetPersonnalise ?? 'CAEI' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 24px 12px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <!-- Contenu principal -->
                    <tr>
                        <td style="padding: 32px 28px; color: #1e293b; font-size: 15px; line-height: 1.65;">
                            {!! $contenuPersonnalise !!}
                        </td>
                    </tr>

                    <!-- Pied de page conforme RGPD & Anti-Spam (RFC, Gmail, Yahoo) -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 24px 28px; border-top: 1px solid #e2e8f0; text-align: center; color: #64748b; font-size: 11px; line-height: 1.5;">
                            <p style="margin: 0 0 6px 0; font-weight: 600; color: #475569;">
                                CAEI &bull; Cabinet d'Audit, d'Expertise et d'Ingénierie
                            </p>
                            <p style="margin: 0 0 10px 0;">
                                Vous recevez cet email car vous êtes inscrit dans notre carnet de contacts professionnels ou avez participé à nos formations/séminaires.
                            </p>
                            @if(isset($contact) && $contact)
                                <p style="margin: 0;">
                                    <a href="{{ route('contact.unsubscribe', ['email' => $contact->email]) }}"
                                       style="color: #4f46e5; text-decoration: underline; font-weight: 500;">
                                        Se désinscrire de ces communications
                                    </a>
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Pixel d'ouverture discret conforme (pas de display:none agressif) -->
    @if(isset($emailLogId) && $emailLogId)
        <img src="{{ route('track.open', ['log_id' => $emailLogId]) }}" width="1" height="1" border="0" alt="" style="width:1px!important;height:1px!important;border:0!important;margin:0!important;padding:0!important;outline:none!important;" />
    @endif
</body>
</html>
