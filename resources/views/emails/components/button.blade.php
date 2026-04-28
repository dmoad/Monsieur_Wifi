{{--
    Bulletproof CTA button — Outlook-safe via VML fallback.
    Usage: @include('emails.components.button', ['url' => '...', 'label' => '...'])
    Optional: 'variant' => 'primary' (default) | 'secondary'
--}}
@php
    $variant = $variant ?? 'primary';
    $bg     = $variant === 'secondary' ? '#5C6370' : '#6366F1';
    $border = $variant === 'secondary' ? '#5C6370' : '#6366F1';
@endphp

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0;">
    <tr>
        <td align="center">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:48px; v-text-anchor:middle; width:240px;" arcsize="17%" stroke="f" fillcolor="{{ $bg }}">
                <w:anchorlock/>
                <center style="color:#FFFFFF; font-family:'Montserrat',Arial,sans-serif; font-size:15px; font-weight:600;">{{ $label }}</center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-- -->
            <a href="{{ $url }}" style="display:inline-block; padding:14px 32px; background-color:{{ $bg }}; color:#FFFFFF; font-family:'Montserrat',Arial,sans-serif; font-size:15px; font-weight:600; text-decoration:none; border-radius:8px; border:1px solid {{ $border }};">
                {{ $label }}
            </a>
            <!--<![endif]-->
        </td>
    </tr>
</table>
