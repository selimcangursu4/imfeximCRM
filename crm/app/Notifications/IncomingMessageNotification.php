<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncomingMessageNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $customerName;
    protected $provider;

    public function __construct($message, $customerName, $provider)
    {
        $this->message = $message;
        $this->customerName = $customerName;
        $this->provider = $provider;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'customer_name' => $this->customerName,
            'provider' => $this->provider,
            'body' => $this->message->body,
            'url' => route('omnichannel.show', $this->message->conversation_id)
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
