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
        $clientName = $notifiable->name ?? $this->reservation->guest_name ?? 'Cher client';

        $reservationDetailUrl = route('client.reservations.show', $this->reservation);
        // For check-out, append #review-section anchor so the client directly reaches the review area on the reservation details page
        $checkoutReviewUrl = route('client.reservations.show', $this->reservation) . '#review-section';

        switch ($status) {
            case 'confirmed':
                $subject = 'Votre réservation est confirmée – HotelBooking';
                $statusLabelFr = 'Confirmée';
                $message = "Nous avons le plaisir de vous informer que votre réservation a été confirmée avec succès. Toute l'équipe de l'établissement prépare votre venue afin de rendre votre séjour des plus agréables.";
                $actionText = 'Voir ma réservation';
                $actionUrl = $reservationDetailUrl;
                break;

            case 'checked_in':
                $subject = 'Confirmation de votre arrivée – HotelBooking';
                $statusLabelFr = 'Arrivée effectuée (Check-in)';
                $message = "Votre arrivée à l'hôtel a été enregistrée avec succès. Nous vous souhaitons un excellent et agréable séjour parmi nous.";
                $actionText = 'Consulter les détails du séjour';
                $actionUrl = $reservationDetailUrl;
                break;

            case 'checked_out':
                $subject = 'Merci pour votre séjour – Partagez votre avis';
                $statusLabelFr = 'Départ effectué (Check-out)';
                $message = "Nous tenons à vous remercier chaleureusement d'avoir séjourné chez nous ! Nous espérons que votre séjour a été à la hauteur de vos attentes. Votre expérience nous intéresse : prenez quelques instants pour partager votre avis et attribuer une note à l'établissement.";
                $actionText = 'Donner mon avis';
                $actionUrl = $checkoutReviewUrl;
                break;

            case 'cancelled':
                $subject = 'Mise à jour de votre réservation – Annulation';
                $statusLabelFr = 'Annulée';
                $message = "Nous vous confirmons que votre réservation a bien été annulée. Si vous avez des questions complémentaires concernant les conditions d'annulation ou votre dossier, n'hésitez pas à nous contacter.";
                $actionText = 'Consulter le dossier';
                $actionUrl = $reservationDetailUrl;
                break;

            default:
                $subject = 'Mise à jour de votre réservation – HotelBooking';
                $statusLabelFr = ucfirst($status);
                $message = "Le statut de votre réservation a été mis à jour.";
                $actionText = 'Voir ma réservation';
                $actionUrl = $reservationDetailUrl;
                break;
        }

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.reservation_status', [
                'reservation'   => $this->reservation,
                'clientName'    => $clientName,
                'statusLabelFr' => $statusLabelFr,
                'statusMessage' => $message,
                'actionText'    => $actionText,
                'actionUrl'     => $actionUrl,
                'subjectTitle'  => $subject,
            ]);
    }
}
