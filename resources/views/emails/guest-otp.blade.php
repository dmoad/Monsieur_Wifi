@extends('emails.layouts.master')

@section('title', __('emails/guest-otp.heading'))
@section('preheader', __('emails/guest-otp.intro'))
@section('headline', __('emails/guest-otp.heading'))
@section('subhead', $brandName)

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/guest-otp.greeting') }}
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {!! __('emails/guest-otp.intro') !!}
    </p>

    {{-- OTP code block --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="background-color:#EEF2FF; border:2px dashed #6366F1; border-radius:12px;">
                    <tr>
                        <td style="padding:20px 40px; text-align:center;">
                            <div style="font-family:'Montserrat',Arial,sans-serif; font-size:36px; font-weight:700; letter-spacing:10px; color:#6366F1; line-height:1;">
                                {{ $otp }}
                            </div>
                            <div style="margin-top:8px; font-size:11px; color:#8B919A; letter-spacing:0.5px; text-transform:uppercase;">
                                {{ __('emails/guest-otp.otp_label') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @include('emails.components.callout', [
        'variant' => 'info',
        'body'    => __('emails/guest-otp.note'),
    ])
@endsection
