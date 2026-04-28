@extends('emails.layouts.master')

@section('title', __('emails/shipping-tracking.heading'))
@section('preheader', __('emails/shipping-tracking.intro_html', ['order_number' => $order->order_number]))
@section('headline', __('emails/shipping-tracking.heading'))

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/shipping-tracking.greeting') }} {{ $order->user->name }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {!! __('emails/shipping-tracking.intro_html', ['order_number' => '<strong>#' . e($order->order_number) . '</strong>']) !!}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0; background-color:#F5F6F9; border-radius:8px;">
        <tr>
            <td style="padding:20px 24px; text-align:center;">
                <div style="font-size:12px; color:#8B919A; letter-spacing:0.4px; text-transform:uppercase; margin-bottom:6px;">
                    {{ __('emails/shipping-tracking.label_provider') }}
                </div>
                <div style="font-size:15px; color:#1A1A2E; font-weight:600; margin-bottom:18px;">
                    {{ $order->shipping_provider }}
                </div>
                <div style="font-size:12px; color:#8B919A; letter-spacing:0.4px; text-transform:uppercase; margin-bottom:6px;">
                    {{ __('emails/shipping-tracking.label_tracking') }}
                </div>
                <div style="font-family:'Montserrat',Arial,sans-serif; font-size:22px; color:#6366F1; font-weight:700; letter-spacing:1px; word-break:break-all;">
                    {{ $order->tracking_id }}
                </div>
            </td>
        </tr>
    </table>

    @include('emails.components.button', [
        'url'   => url('/' . app()->getLocale() . '/orders/' . $order->order_number),
        'label' => __('emails/shipping-tracking.btn_track'),
    ])
@endsection
