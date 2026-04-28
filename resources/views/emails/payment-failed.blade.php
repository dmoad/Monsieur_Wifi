@extends('emails.layouts.master')

@section('title', __('emails/payment-failed.heading'))
@section('preheader', __('emails/payment-failed.body_retry'))
@section('headline', __('emails/payment-failed.heading'))

@section('content')
    @include('emails.components.callout', [
        'variant' => 'danger',
        'body'    => __('emails/payment-failed.intro_html', ['order_number' => '<strong>#' . e($order->order_number) . '</strong>']),
    ])

    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/payment-failed.greeting') }} {{ $order->user->name }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/payment-failed.body_retry') }}
    </p>

    @include('emails.components.button', [
        'url'   => url('/' . app()->getLocale() . '/orders/' . $order->order_number),
        'label' => __('emails/payment-failed.btn_retry'),
    ])
@endsection
