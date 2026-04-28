@extends('emails.layouts.master')

@section('title', __('emails/cart-abandonment.heading'))
@section('preheader', __('emails/cart-abandonment.intro'))
@section('headline', __('emails/cart-abandonment.heading'))

@section('content')
    <p style="margin:0 0 16px; font-size:15px; color:#1A1A2E;">
        {{ __('emails/cart-abandonment.greeting') }} {{ $cart->user->name }},
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:#5C6370; line-height:1.6;">
        {{ __('emails/cart-abandonment.intro') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; margin:16px 0; background-color:#F5F6F9; border-radius:8px;">
        @foreach($cart->items as $item)
            <tr>
                <td style="padding:12px 16px; border-bottom:{{ $loop->last ? 'none' : '1px solid #E5E8ED' }}; color:#1A1A2E; font-size:14px;">
                    {{ $item->productModel->name }} <span style="color:#8B919A;">× {{ $item->quantity }}</span>
                </td>
                <td style="padding:12px 16px; border-bottom:{{ $loop->last ? 'none' : '1px solid #E5E8ED' }}; color:#1A1A2E; font-size:14px; font-weight:600; text-align:right; white-space:nowrap;">
                    €{{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin:24px 0; text-align:center; font-size:18px; color:#6366F1;">
        <strong>{{ __('emails/cart-abandonment.label_total') }} €{{ number_format($cart->getTotal(), 2) }}</strong>
    </p>

    @include('emails.components.button', [
        'url'   => url('/' . app()->getLocale() . '/cart'),
        'label' => __('emails/cart-abandonment.btn_complete'),
    ])
@endsection
