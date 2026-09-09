<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $objetPersonnalise ?? 'CAEI COMPANY GROUP' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; color: #0f172a;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 28px 12px;">
                <!-- Main Card Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);">
                    <!-- Header avec Logo Officiel CAEI -->
                    <tr>
                        <td align="center" style="padding: 32px 24px 22px 24px; background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);">
                            <a href="https://www.caei-afri.com" target="_blank" style="text-decoration: none; display: inline-block;">
                                <img src="{{ asset('images/logo-caei.jpg') }}" 
                                     alt="CAEI COMPANY GROUP" 
                                     width="115" 
                                     style="display: block; width: 115px; max-width: 115px; height: auto; border: 0; margin: 0 auto; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.08);" />
                            </a>
                            <div style="margin-top: 12px; font-size: 13px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #0f172a;">
                                CAEI COMPANY GROUP
                            </div>
                            <div style="font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #b45309; margin-top: 2px;">
                                Codicil &bull; Audit &bull; Formation &bull; Conseil
                            </div>
                            <!-- Gold accent bar -->
                            <div style="height: 2px; width: 60px; background: linear-gradient(90deg, #b45309, #f59e0b); margin: 16px auto 0 auto; border-radius: 2px;"></div>
                        </td>
                    </tr>

                    <!-- Contenu du message -->
                    <tr>
                        <td style="padding: 32px 32px 28px 32px; color: #1e293b; font-size: 15px; line-height: 1.75;">
                            {!! $contenuPersonnalise !!}
                        </td>
                    </tr>

                    <!-- Pied de page officiel prestigieux -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 26px 28px; border-top: 1px solid #e2e8f0; text-align: center; color: #64748b; font-size: 11px; line-height: 1.6;">
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155; font-size: 12px;">
                                CAEI COMPANY GROUP
                            </p>
                            <p style="margin: 0 0 8px 0; color: #64748b;">
                                Cabinet International d'Audit, d'Expertise et d'Ingénierie de Formation
                            </p>
                            <p style="margin: 0 0 12px 0;">
                                <a href="https://www.caei-afri.com" target="_blank" style="color: #b45309; text-decoration: none; font-weight: 600;">
                                    www.caei-afri.com
                                </a>
                                &nbsp;&bull;&nbsp;
                                <a href="mailto:Contact@caei-afri.com" style="color: #64748b; text-decoration: none;">
                                    Contact@caei-afri.com
                                </a>
                            </p>
                            <p style="margin: 0 0 12px 0; color: #94a3b8; font-size: 10px;">
                                Vous recevez cette communication professionnelle car vous êtes inscrit dans le réseau de contacts CAEI.
                            </p>
                            @if(isset($contact) && $contact)
                                <p style="margin: 0; padding-top: 8px; border-top: 1px dashed #e2e8f0;">
                                    <a href="{{ route('contact.unsubscribe', ['email' => $contact->email]) }}"
                                       style="color: #94a3b8; text-decoration: underline; font-size: 10px;">
                                        Se désinscrire de cette liste
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
