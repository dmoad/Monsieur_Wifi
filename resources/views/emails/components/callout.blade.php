{{--
    Inline tone callout — colored left-border banner.
    Usage: @include('emails.components.callout', ['variant' => 'success', 'body' => 'text or HTML'])
    Variants: info (default) | success | warning | danger
--}}
@php
    $palette = [
        'info'    => ['bg' => '#EEF2FF', 'border' => '#6366F1', 'text' => '#1A1A2E'],
        'success' => ['bg' => '#E7F8EF', 'border' => '#10B981', 'text' => '#065F46'],
        'warning' => ['bg' => '#FEF3C7', 'border' => '#F59E0B', 'text' => '#7C2D12'],
        'danger'  => ['bg' => '#FEE2E2', 'border' => '#EF4444', 'text' => '#7F1D1D'],
    ];
    $v = $palette[$variant ?? 'info'] ?? $palette['info'];
@endphp

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0; background-color:{{ $v['bg'] }}; border-left:3px solid {{ $v['border'] }}; border-radius:6px;">
    <tr>
        <td style="padding:14px 16px; font-size:13px; color:{{ $v['text'] }}; line-height:1.5;">
            {!! $body !!}
        </td>
    </tr>
</table>
