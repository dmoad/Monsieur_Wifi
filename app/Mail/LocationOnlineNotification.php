<?php

namespace App\Mail;

use App\Models\Device;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

class LocationOnlineNotification extends Mailable
{
    use Queueable;

    public Location $location;

    public ?Device $device;

    public Carbon $backOnlineAt;

    /** Translation locale for subjects/strings (not {@see Mailable::$locale}, which is reserved). */
    public string $preferredLocale;

    public function __construct(Location $location, ?Device $device, Carbon $backOnlineAt, string $preferredLocale = 'en')
    {
        $this->location = $location;
        $this->device = $device;
        $this->backOnlineAt = $backOnlineAt;
        $this->preferredLocale = $preferredLocale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails/location-online-notification.subject', ['location' => $this->location->name], $this->preferredLocale),
        );
    }

    public function content(): Content
    {
        $backOnlineLabel = $this->backOnlineAt->copy()->timezone(config('app.timezone'))->format('Y-m-d H:i T');

        $address = trim(implode(', ', array_filter([
            $this->location->address,
            $this->location->city,
            $this->location->postal_code,
            $this->location->country,
        ])));

        $rows = [
            ['label' => __('emails/location-online-notification.label_name', [], $this->preferredLocale), 'value' => e($this->location->name)],
            ['label' => __('emails/location-online-notification.label_address', [], $this->preferredLocale), 'value' => e($address !== '' ? $address : '—')],
            ['label' => __('emails/location-online-notification.label_back_online_at', [], $this->preferredLocale), 'value' => e($backOnlineLabel)],
        ];

        if ($this->device) {
            $rows[] = ['label' => __('emails/location-online-notification.label_device', [], $this->preferredLocale), 'value' => e($this->device->name ?: ('#'.$this->device->id))];
        }

        return new Content(
            view: 'emails.location-online-notification',
            with: [
                'location' => $this->location,
                'device' => $this->device,
                'backOnlineLabel' => $backOnlineLabel,
                'kvRows' => $rows,
                'locale' => $this->preferredLocale,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
