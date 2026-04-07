<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class QuotaAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $metricName,
        private readonly int|float $current,
        private readonly int $limit,
        private readonly string $unit,
        private readonly float $percentage,
        private readonly string $severity
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $severityColors = [
            'critical' => '#DC2626', // Red
            'high' => '#EA580C',     // Orange
            'medium' => '#CA8A04',   // Yellow
            'low' => '#16A34A',      // Green
        ];

        $severityLabels = [
            'critical' => 'KRITIS',
            'high' => 'TINGGI',
            'medium' => 'MENENGAH',
            'low' => 'RENDAH',
        ];

        $actionText = match ($this->severity) {
            'critical' => 'Upgrade Plan Sekarang',
            'high' => 'Pertimbangkan Upgrade',
            default => 'Lihat Penggunaan',
        };

        return (new MailMessage)
            ->subject("⚠️ [{$severityLabels[$this->severity]}] Batas Penggunaan {$this->metricName} Tercapai")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda menerima notifikasi ini karena penggunaan {$this->metricName} telah mencapai {$this->percentage}% dari batas yang diizinkan.")
            ->line('')
            ->line('Detail Penggunaan:')
            ->line("• Metrik: {$this->metricName}")
            ->line("• Penggunaan Saat Ini: {$this->current} {$this->unit}")
            ->line("• Batas Maksimum: {$this->limit} {$this->unit}")
            ->line("• Persentase: {$this->percentage}%")
            ->line('')
            ->line('Tingkat: ' . $severityLabels[$this->severity])
            ->action($actionText, URL::route('settings.billing'))
            ->line('')
            ->line('Jika Anda membutuhkan batas yang lebih tinggi, silakan upgrade plan Anda atau hubungi tim support kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'metric_name' => $this->metricName,
            'current' => $this->current,
            'limit' => $this->limit,
            'unit' => $this->unit,
            'percentage' => $this->percentage,
            'severity' => $this->severity,
            'type' => 'quota_alert',
        ];
    }
}