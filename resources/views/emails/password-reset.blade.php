@extends('emails.layouts.master')

@section('title', 'Password Reset — Monsieur WiFi')
@section('preheader', 'Reset your Monsieur WiFi password.')
@section('headline', 'Reset your password')

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        Hello{{ $userName ? ' ' . $userName : '' }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        We received a request to reset the password for your Monsieur WiFi account. If this was you, click the button below to set a new password.
    </p>

    @include('emails.components.button', [
        'url'   => $resetUrl,
        'label' => 'Reset my password',
    ])

    <p style="margin:32px 0 8px; font-size:13px; color:#5C6370;">
        If the button doesn't work, copy and paste this link into your browser:
    </p>
    <p style="margin:0 0 24px; font-size:12px; color:#6366F1; word-break:break-all; line-height:1.5;">
        <a href="{{ $resetUrl }}" style="color:#6366F1; text-decoration:none;">{{ $resetUrl }}</a>
    </p>

    @include('emails.components.callout', [
        'variant' => 'warning',
        'body'    => 'This link expires in <strong>' . $expiresIn . ' minutes</strong>. If you didn\'t request a password reset, you can safely ignore this email.',
    ])

    <p style="margin:24px 0 8px; font-size:13px; color:#8B919A;">For your security, we recommend:</p>
    <ul style="margin:0; padding-left:20px; font-size:13px; color:#5C6370; line-height:1.7;">
        <li>Using a strong, unique password</li>
        <li>Not sharing your password with anyone</li>
        <li>Changing your password regularly</li>
    </ul>
@endsection
