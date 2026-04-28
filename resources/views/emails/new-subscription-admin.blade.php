@extends('emails.layouts.master')

@section('title', 'Nouvel abonnement — Monsieur WiFi')
@section('preheader', 'Un utilisateur vient de souscrire à un abonnement.')
@section('headline', 'Nouvel abonnement')
@section('subhead', 'Un utilisateur vient de souscrire')

@section('content')
    <h3 style="margin:0 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        Informations client
    </h3>
    @include('emails.components.kv-table', [
        'rows' => [
            ['label' => 'Nom',                'value' => e($user->name)],
            ['label' => 'Email',              'value' => e($user->email)],
            ['label' => 'Date d\'inscription','value' => e($user->created_at->format('d/m/Y H:i'))],
        ],
    ])

    <h3 style="margin:24px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        Détails de l'abonnement
    </h3>
    @include('emails.components.kv-table', [
        'rows' => [
            ['label' => 'Offre',                'value' => e($subscriptionData['plan_name'])],
            ['label' => 'Montant',              'value' => e($subscriptionData['amount'])],
            ['label' => 'Période',              'value' => e($subscriptionData['interval'])],
            ['label' => 'Date de souscription', 'value' => e($subscriptionData['start_date'])],
        ],
    ])

    @if(!empty($subscriptionData['shipping_address']))
        <h3 style="margin:24px 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
            Adresse de livraison
        </h3>
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:16px 0; background-color:#F5F6F9; border-radius:8px;">
            <tr>
                <td style="padding:14px 16px; font-size:14px; color:#5C6370; line-height:1.7;">
                    {{ $subscriptionData['shipping_address']['name'] }}<br>
                    {{ $subscriptionData['shipping_address']['line1'] }}
                    @if($subscriptionData['shipping_address']['line2'])<br>{{ $subscriptionData['shipping_address']['line2'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['postal_code'] }} {{ $subscriptionData['shipping_address']['city'] }}
                    @if($subscriptionData['shipping_address']['state'])<br>{{ $subscriptionData['shipping_address']['state'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['country'] }}
                </td>
            </tr>
        </table>
    @endif
@endsection
