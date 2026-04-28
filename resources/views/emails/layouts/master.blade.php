{{--
    Master email layout — table-based for Outlook/Word-engine compatibility.
    Design tokens are baked in (no CSS vars in email).

    Slots:
        @section('preheader')      Hidden preview text shown by inbox lists. Keep < 90 chars.
        @section('headline')       Brand-bar headline (renders inside the indigo header).
        @section('subhead')        Brand-bar subline (smaller, optional).
        @section('content')        Main body. Use components/heading, components/paragraph, components/button.

    Anything not slot-overridden falls back to a neutral default.
--}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'Monsieur WiFi')</title>
    <style type="text/css">
        /* Resets */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        a { text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* Mobile */
        @media screen and (max-width: 600px) {
            .mw-container { width: 100% !important; }
            .mw-pad-x { padding-left: 24px !important; padding-right: 24px !important; }
            .mw-headline { font-size: 22px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#EDEEF2; font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    {{-- Preheader: hidden, shown in inbox preview only --}}
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#EDEEF2;">
        @yield('preheader', '')
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#EDEEF2;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" class="mw-container" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px; max-width:600px; background-color:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                    {{-- Brand bar --}}
                    <tr>
                        <td class="mw-pad-x" align="center" style="background-color:#6366F1; padding:36px 32px; color:#FFFFFF;">
                            <div style="font-size:14px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; opacity:0.85; margin-bottom:8px;">
                                Monsieur WiFi
                            </div>
                            <h1 class="mw-headline" style="margin:0; font-size:26px; font-weight:600; line-height:1.25; color:#FFFFFF;">
                                @yield('headline')
                            </h1>
                            @hasSection('subhead')
                                <div style="margin-top:8px; font-size:14px; opacity:0.85;">@yield('subhead')</div>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td class="mw-pad-x" style="padding:36px 32px; color:#1A1A2E; font-size:15px; line-height:1.6;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="mw-pad-x" style="padding:24px 32px; background-color:#F5F6F9; border-top:1px solid #E5E8ED; text-align:center; color:#8B919A; font-size:12px; line-height:1.5;">
                            <div style="font-weight:600; color:#5C6370; margin-bottom:4px;">Monsieur WiFi</div>
                            <div>WiFi Network Management System</div>
                            <div style="margin-top:12px;">&copy; {{ date('Y') }} Monsieur WiFi. All rights reserved.</div>
                            <div style="margin-top:8px;">
                                <a href="{{ config('app.url') }}" style="color:#6366F1; text-decoration:none; font-weight:500;">{{ config('app.url') }}</a>
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
