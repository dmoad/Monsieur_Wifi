@extends('emails.layouts.master')

@section('title', __('emails/order-delivered.heading'))
@section('preheader', __('emails/order-delivered.body_thanks'))
@section('headline', __('emails/order-delivered.heading'))

@section('content')
    @php
        $orderUrl = url('/' . app()->getLocale() . '/orders/' . $order->order_number);
        $orderLink = '<strong><a href="' . e($orderUrl) . '" style="color:#6366F1; text-decoration:none;">#' . e($order->order_number) . '</a></strong>';
    @endphp

    @include('emails.components.callout', [
        'variant' => 'success',
        'body'    => '<strong>' . __('emails/order-delivered.heading') . '</strong>',
    ])

    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/order-delivered.greeting') }} {{ $order->user->name }},
    </p>

    <p style="margin:0 0 16px; font-size:15px; color:#5C6370; line-height:1.6;">
        {!! __('emails/order-delivered.intro_html', ['order_link' => $orderLink]) !!}
    </p>

    <p style="margin:0 0 16px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/order-delivered.body_enjoy') }}
    </p>

    <p style="margin:0 0 16px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/order-delivered.body_thanks') }}
    </p>
@endsection
