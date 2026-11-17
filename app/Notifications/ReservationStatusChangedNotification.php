<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusChangedNotification extends Notification
{
    use Queueable;

    public Reservation $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        // Ensure relationships are loaded if available
        $this->reservation = $reservation->relationLoaded('hotel') && $reservation->relationLoaded('room')
            ? $reservation
            : $reservation->loadMissing(['hotel', 'room', 'user']);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->reservation->status;
        $clientName = (!empty($notifiable->name) && trim($notifiable->name) !== '') 
            ? $notifiable->name 
            : ($this->reservation->guest_name ?? 'Valued Guest');

        $reservationDetailUrl = route('client.reservations.show', $this->reservation);
        // For check-out, append #review-section anchor so the client directly reaches the review area on the reservation details page
        $checkoutReviewUrl = route('client.reservations.show', $this->reservation) . '#review-section';

        switch ($status) {
            case 'confirmed':
                $subject = 'Your Reservation is Confirmed – HotelBooking';
                $statusLabel = 'Confirmed';
                $message = "We are pleased to inform you that your reservation has been confirmed successfully. Our team looks forward to welcoming you and ensuring you have an exceptional stay.";
                $actionText = 'View Reservation';
                $actionUrl = $reservationDetailUrl;
                break;

            case 'checked_in':
                $subject = 'Check-in Confirmation – HotelBooking';
                $statusLabel = 'Checked In';
                $message = "Your check-in has been successfully registered. We wish you a wonderful and relaxing stay with us.";
                $actionText = 'View Stay Details';
                $actionUrl = $reservationDetailUrl;
                break;

            case 'checked_out':
                $subject = 'Thank You for Your Stay – Share Your Review';
                $statusLabel = 'Checked Out';
                $message = "Thank you for staying with us! We hope you enjoyed your time at our property. Your feedback is very important to us: please take a moment to share your review and rate your experience.";
                $actionText = 'Leave a Review';
                $actionUrl = $checkoutReviewUrl;
                break;

            case 'cancelled':
                $subject = 'Reservation Cancellation Notice – HotelBooking';
                $statusLabel = 'Cancelled';
                $message = "We confirm that your reservation has been cancelled. If you have any questions regarding your cancellation or booking terms, please do not hesitate to contact us.";
                $actionText = 'View Reservation Details';
                $actionUrl = $reservationDetailUrl;
                break;

            default:
                $subject = 'Reservation Update – HotelBooking';
                $statusLabel = ucfirst(str_replace('_', ' ', $status));
                $message = "The status of your reservation has been updated.";
                $actionText = 'View Reservation';
                $actionUrl = $reservationDetailUrl;
                break;
        }

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.reservation_status', [
                'reservation'   => $this->reservation,
                'clientName'    => $clientName,
                'statusLabel'   => $statusLabel,
                'statusLabelFr' => $statusLabel,
                'statusMessage' => $message,
                'actionText'    => $actionText,
                'actionUrl'     => $actionUrl,
                'subjectTitle'  => $subject,
            ]);
    }
}
