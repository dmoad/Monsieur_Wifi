@extends('emails.layouts.master')

@section('title', __('emails/order-processed.title'))
@section('preheader', __('emails/order-processed.intro'))
@section('headline', __('emails/order-processed.heading'))
@section('subhead', __('emails/order-processed.subheading'))

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/order-processed.greeting') }} {{ $order->user->name }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/order-processed.intro') }}
    </p>

    <p style="margin:24px 0 8px; font-size:13px; color:#8B919A; letter-spacing:0.4px; text-transform:uppercase;">
        {{ __('emails/order-processed.order_number_prefix') }}
    </p>
    <p style="margin:0 0 24px; font-size:22px; color:#6366F1; font-weight:700;">
        #{{ $order->order_number }}
    </p>

    <h3 style="margin:24px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        {{ __('emails/order-processed.summary_heading') }}
    </h3>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; background-color:#F5F6F9; border-radius:8px;">
        @foreach($order->items as $item)
            <tr>
                <td style="padding:12px 16px; border-bottom:1px solid #E5E8ED; color:#1A1A2E; font-size:14px;">
                    {{ $item->productModel->name }} <span style="color:#8B919A;">× {{ $item->quantity }}</span>
                </td>
                <td style="padding:12px 16px; border-bottom:1px solid #E5E8ED; color:#1A1A2E; font-size:14px; font-weight:600; text-align:right; white-space:nowrap;">
                    €{{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td style="padding:10px 16px; color:#5C6370; font-size:13px;">{{ __('emails/order-processed.label_subtotal') }}</td>
            <td style="padding:10px 16px; color:#1A1A2E; font-size:13px; text-align:right; white-space:nowrap;">€{{ number_format($order->product_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:6px 16px; color:#5C6370; font-size:13px;">{{ __('emails/order-processed.label_shipping') }}</td>
            <td style="padding:6px 16px; color:#1A1A2E; font-size:13px; text-align:right; white-space:nowrap;">€{{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:6px 16px; color:#5C6370; font-size:13px;">{{ __('emails/order-processed.label_tax') }}</td>
            <td style="padding:6px 16px; color:#1A1A2E; font-size:13px; text-align:right; white-space:nowrap;">€{{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; border-top:2px solid #6366F1; color:#1A1A2E; font-size:15px; font-weight:700;">
                {{ __('emails/order-processed.label_total') }}
            </td>
            <td style="padding:12px 16px; border-top:2px solid #6366F1; color:#6366F1; font-size:18px; font-weight:700; text-align:right; white-space:nowrap;">
                €{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>

    <h3 style="margin:32px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        {{ __('emails/order-processed.shipping_heading') }}
    </h3>
    <p style="margin:0 0 24px; font-size:14px; color:#5C6370; line-height:1.6;">
        {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
        {{ $order->shippingAddress->address_line1 }}<br>
        @if($order->shippingAddress->address_line2){{ $order->shippingAddress->address_line2 }}<br>@endif
        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }} {{ $order->shippingAddress->postal_code }}<br>
        {{ $order->shippingAddress->country }}
    </p>

    @include('emails.components.button', [
        'url'   => url('/' . app()->getLocale() . '/orders/' . $order->order_number),
        'label' => __('emails/order-processed.btn_view_details'),
    ])
@endsection
