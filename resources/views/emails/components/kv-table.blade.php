{{--
    Key/value detail table.
    Usage: @include('emails.components.kv-table', [
        'rows' => [
            ['label' => 'Plan', 'value' => 'Pro'],
            ['label' => 'Amount', 'value' => '€19.99'],
        ],
    ])
--}}
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; margin:16px 0; background-color:#F5F6F9; border-radius:8px;">
    @foreach($rows as $i => $row)
        <tr>
            <td style="padding:12px 16px; border-bottom:{{ $i === array_key_last($rows) ? 'none' : '1px solid #E5E8ED' }}; color:#5C6370; font-size:13px; line-height:1.4;">
                {{ $row['label'] }}
            </td>
            <td style="padding:12px 16px; border-bottom:{{ $i === array_key_last($rows) ? 'none' : '1px solid #E5E8ED' }}; color:#1A1A2E; font-size:13px; font-weight:600; text-align:right; line-height:1.4;">
                {!! $row['value'] !!}
            </td>
        </tr>
    @endforeach
</table>
