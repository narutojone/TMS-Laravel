<?php

namespace App\Notifications;

use App\Repositories\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification
{
	use Queueable;

    private $tempPassword;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($tempPassword = null)
	{
		$this->tempPassword = $tempPassword;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$message = (new MailMessage)
			->line('You are receiving this email because a user has been created for you.');

		if ($notifiable->role == User::ROLE_ADMIN || $notifiable->role == User::ROLE_CUSTOMER_SERVICE) {
			$message->line('You are an administrator.');
		}

        $message->line('Your current password is: ' . $this->tempPassword);

		$message
			->action('Login', route('login'))
			->line('If you believe you received this email by mistake, no further action is required.');

		return $message;
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
