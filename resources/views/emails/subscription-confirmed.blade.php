@extends('emails.layouts.master')

@section('title', __('emails/subscription-confirmed.title'))
@section('preheader', __('emails/subscription-confirmed.intro'))
@section('headline', __('emails/subscription-confirmed.heading'))
@section('subhead', __('emails/subscription-confirmed.subheading'))

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/subscription-confirmed.greeting') }} {{ $user->name }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/subscription-confirmed.intro') }}
    </p>

    <h3 style="margin:24px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        {{ __('emails/subscription-confirmed.details_heading') }}
    </h3>
    @include('emails.components.kv-table', [
        'rows' => [
            ['label' => __('emails/subscription-confirmed.label_plan'),           'value' => e($subscriptionData['plan_name'])],
            ['label' => __('emails/subscription-confirmed.label_amount'),         'value' => e($subscriptionData['amount'])],
            ['label' => __('emails/subscription-confirmed.label_billing_period'), 'value' => e($subscriptionData['interval'])],
            ['label' => __('emails/subscription-confirmed.label_start_date'),     'value' => e($subscriptionData['start_date'])],
        ],
    ])

    @if(!empty($subscriptionData['shipping_address']))
        <h3 style="margin:24px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
            {{ __('emails/subscription-confirmed.shipping_heading') }}
        </h3>
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:16px 0; background-color:#F5F6F9; border-radius:8px;">
            <tr>
                <td style="padding:14px 16px; font-size:14px; color:#5C6370; line-height:1.7;">
                    {{ $subscriptionData['shipping_address']['name'] }}<br>
                    {{ $subscriptionData['shipping_address']['line1'] }}
                    @if($subscriptionData['shipping_address']['line2'])<br>{{ $subscriptionData['shipping_address']['line2'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['postal_code'] }} {{ $subscriptionData['shipping_address']['city'] }}
                    @if($subscriptionData['shipping_address']['state']), {{ $subscriptionData['shipping_address']['state'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['country'] }}
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:24px 0; font-size:13px; color:#8B919A; line-height:1.6;">
        {{ __('emails/subscription-confirmed.manage_hint') }}
    </p>

    @include('emails.components.button', [
        'url'   => url('/' . app()->getLocale() . '/profile'),
        'label' => __('emails/subscription-confirmed.btn_dashboard'),
    ])

    <p style="margin:24px 0 0; font-size:13px; color:#8B919A; line-height:1.6;">
        {!! __('emails/subscription-confirmed.support_hint_html', ['email' => '<a href="mailto:support@monsieur-wifi.com" style="color:#6366F1; text-decoration:none;">support@monsieur-wifi.com</a>']) !!}
    </p>
@endsection
