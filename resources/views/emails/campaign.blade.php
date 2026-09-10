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
                    <!-- Header Officiel CAEI : Coordonnées à gauche + Logo à droite -->
                    @php
                        $headerSettings = \App\Services\EmailHeaderSettings::get();
                    @endphp
                    @if($headerSettings['show_header'] ?? true)
                    <tr>
                        <td style="padding: 24px 28px 20px 28px; background-color: #ffffff; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- Coordonnées à gauche -->
                                    <td valign="middle" style="text-align: left; vertical-align: middle; padding-right: 12px; font-family: Arial, Helvetica, sans-serif;">
                                        @if(!empty($headerSettings['company_name']))
                                            <div style="font-size: 14px; font-weight: bold; font-style: italic; color: #0f172a; margin-bottom: 5px; letter-spacing: 0.3px;">
                                                {{ $headerSettings['company_name'] }}
                                            </div>
                                        @endif
                                        <div style="font-size: 11px; line-height: 1.55; color: #1e293b; font-style: italic;">
                                            @if(!empty($headerSettings['siege_social']))
                                                <div><strong>Siège social :</strong> {{ $headerSettings['siege_social'] }}</div>
                                            @endif
                                            @if(!empty($headerSettings['telephone']))
                                                <div><strong>Téléphone :</strong> {{ $headerSettings['telephone'] }}</div>
                                            @endif
                                            @if(!empty($headerSettings['email']))
                                                <div><strong>E-mail :</strong> <a href="mailto:{{ $headerSettings['email'] }}" style="color: #0f172a; text-decoration: none;">{{ $headerSettings['email'] }}</a></div>
                                            @endif
                                            @if(!empty($headerSettings['site_web']))
                                                <div><strong>Site :</strong> <a href="{{ str_starts_with($headerSettings['site_web'], 'http') ? $headerSettings['site_web'] : 'https://' . $headerSettings['site_web'] }}" target="_blank" style="color: #0f172a; text-decoration: none;">{{ $headerSettings['site_web'] }}</a></div>
                                            @endif
                                            @if(!empty($headerSettings['matricule_fiscal']))
                                                <div><span style="text-decoration: underline;"><strong>MF:</strong></span> {{ $headerSettings['matricule_fiscal'] }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Logo officiel à droite -->
                                    <td valign="middle" align="right" style="width: 125px; min-width: 110px; text-align: right; vertical-align: middle;">
                                        @if(!empty($headerSettings['site_web']))
                                            <a href="{{ str_starts_with($headerSettings['site_web'], 'http') ? $headerSettings['site_web'] : 'https://' . $headerSettings['site_web'] }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                        @endif
                                        <img src="{{ $headerSettings['logo_url'] }}" 
                                             alt="{{ $headerSettings['company_name'] ?? 'CAEI' }}" 
                                             width="115" 
                                             style="display: block; width: 115px; max-width: 115px; height: auto; border: 0; margin-left: auto;" />
                                        @if(!empty($headerSettings['site_web']))
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- Contenu du message -->
                    <tr>
                        <td style="padding: 32px 32px 28px 32px; color: #1e293b; font-size: 15px; line-height: 1.75;">
                            {!! $contenuPersonnalise !!}
                        </td>
                    </tr>

                    <!-- Pied de page officiel prestigieux avec Logo CAEI -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 26px 28px; border-top: 1px solid #e2e8f0; text-align: center; color: #64748b; font-size: 11px; line-height: 1.6;">
                            @if(!empty($headerSettings['footer_logo_url']) && ($headerSettings['show_footer_logo'] ?? true))
                                <div style="margin: 0 auto 16px auto; text-align: center;">
                                    @if(!empty($headerSettings['site_web']))
                                        <a href="{{ str_starts_with($headerSettings['site_web'], 'http') ? $headerSettings['site_web'] : 'https://' . $headerSettings['site_web'] }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                    @endif
                                    <img src="{{ $headerSettings['footer_logo_url'] }}" 
                                         alt="{{ $headerSettings['company_name'] ?? 'CAEI' }}" 
                                         width="260" 
                                         style="display: block; width: 260px; max-width: 85%; height: auto; border: 0; margin: 0 auto;" />
                                    @if(!empty($headerSettings['site_web']))
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155; font-size: 12px;">
                                {{ $headerSettings['company_name'] ?? 'CAEI COMPANY GROUP' }}
                            </p>
                            <p style="margin: 0 0 8px 0; color: #64748b;">
                                Cabinet International d'Audit, d'Expertise et d'Ingénierie de Formation
                            </p>
                            <p style="margin: 0 0 12px 0;">
                                @if(!empty($headerSettings['site_web']))
                                    <a href="{{ str_starts_with($headerSettings['site_web'], 'http') ? $headerSettings['site_web'] : 'https://' . $headerSettings['site_web'] }}" target="_blank" style="color: #b45309; text-decoration: none; font-weight: 600;">
                                        {{ $headerSettings['site_web'] }}
                                    </a>
                                    &nbsp;&bull;&nbsp;
                                @endif
                                @if(!empty($headerSettings['email']))
                                    <a href="mailto:{{ $headerSettings['email'] }}" style="color: #64748b; text-decoration: none;">
                                        {{ $headerSettings['email'] }}
                                    </a>
                                @endif
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
