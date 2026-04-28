@extends('emails.layouts.master')

@section('title', 'Verify Your Email — Monsieur WiFi')
@section('preheader', 'Confirm your email address to activate your Monsieur WiFi account.')
@section('headline', 'Verify your email address')

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        Hello{{ $userName ? ' ' . $userName : '' }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        Thanks for registering with Monsieur WiFi. Confirm your email address to activate your account.
    </p>

    @include('emails.components.button', [
        'url'   => $verificationUrl,
        'label' => 'Verify my email',
    ])

    <p style="margin:32px 0 8px; font-size:13px; color:#5C6370;">
        If the button doesn't work, copy and paste this link into your browser:
    </p>
    <p style="margin:0 0 24px; font-size:12px; color:#6366F1; word-break:break-all; line-height:1.5;">
        <a href="{{ $verificationUrl }}" style="color:#6366F1; text-decoration:none;">{{ $verificationUrl }}</a>
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0; background-color:#EEF2FF; border-left:3px solid #6366F1; border-radius:6px;">
        <tr>
            <td style="padding:14px 16px; font-size:13px; color:#1A1A2E; line-height:1.5;">
                This link expires in <strong>{{ $expiresIn }} minutes</strong>. If you didn't create an account, you can safely ignore this email.
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 8px; font-size:13px; color:#8B919A;">Once verified, you'll be able to:</p>
    <ul style="margin:0; padding-left:20px; font-size:13px; color:#5C6370; line-height:1.7;">
        <li>Create and customize your captive portal</li>
        <li>Manage your WiFi locations</li>
        <li>Monitor guest network usage</li>
        <li>Access all platform features</li>
    </ul>
@endsection
