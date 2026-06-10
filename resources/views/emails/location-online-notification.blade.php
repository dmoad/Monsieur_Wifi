@extends('emails.layouts.master')

@section('title', __('emails/location-online-notification.title', [], $locale))
@section('preheader', __('emails/location-online-notification.preheader', [], $locale))
@section('headline', __('emails/location-online-notification.headline', [], $locale))
@section('subhead', __('emails/location-online-notification.subhead', [], $locale))

@section('content')
    <p style="margin:0 0 16px; font-size:14px; color:#5C6370; line-height:1.6;">
        {{ __('emails/location-online-notification.body', [], $locale) }}
    </p>

    <h3 style="margin:0 0 8px; font-size:15px; color:#1A1A2E; font-weight:600;">
        {{ __('emails/location-online-notification.section_location', [], $locale) }}
    </h3>
    @include('emails.components.kv-table', ['rows' => $kvRows])
@endsection
