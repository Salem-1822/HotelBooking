<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectTitle ?? 'Notification de réservation' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            min-width: 100%;
            background-color: #F8FAFC;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #334155;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #E2E8F0;
        }
        .header {
            background-color: #0F172A;
            padding: 36px 32px 30px;
            text-align: center;
        }
        .brand-title {
            color: #D4AF37;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0;
        }
        .brand-sub {
            color: #94A3B8;
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 6px;
        }
        .content {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 16px;
        }
        .message-lead {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 24px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
        }
        .badge-confirmed {
            background-color: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        .badge-checked_in {
            background-color: #DBEAFE;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
        }
        .badge-checked_out {
            background-color: #F1F5F9;
            color: #334155;
            border: 1px solid #E2E8F0;
        }
        .badge-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }
        .details-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .detail-row {
            padding: 8px 0;
            border-bottom: 1px dashed #CBD5E1;
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-row:first-child {
            padding-top: 0;
        }
        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            width: 40%;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #0F172A;
            text-align: right;
            width: 60%;
        }
        .action-container {
            text-align: center;
            padding: 12px 0 28px;
        }
        .btn-action {
            display: inline-block;
            background-color: #0F172A;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2);
        }
        .btn-accent {
            background-color: #D4AF37;
            color: #0F172A !important;
        }
        .footer {
            background-color: #F1F5F9;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
        }
        .footer-team {
            font-size: 14px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 6px;
        }
        .footer-note {
            font-size: 12px;
            color: #94A3B8;
            margin: 0;
            line-height: 1.5;
        }
        .review-banner {
            background-color: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
        }
        .review-banner-text {
            font-size: 14px;
            color: #92400E;
            font-weight: 600;
            margin: 0;
        }
    </style>
</head>
<body style="margin: 0; padding: 30px 15px; background-color: #F8FAFC;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table class="email-container" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <!-- HEADER -->
                    <tr>
                        <td class="header">
                            <h1 class="brand-title">HotelBooking</h1>
                            <div class="brand-sub">Service Réservation</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td class="content">
                            <h2 class="greeting">Bonjour {{ $clientName }},</h2>

                            <div style="text-align: left;">
                                <span class="status-badge badge-{{ $reservation->status }}">
                                    Statut : {{ $statusLabelFr }}
                                </span>
                            </div>

                            <p class="message-lead">
                                {!! nl2br(e($statusMessage)) !!}
                            </p>

                            @if($reservation->status === 'checked_out')
                            <div class="review-banner">
                                <p class="review-banner-text">
                                    ★ Votre retour d'expérience est précieux pour nous et aide la communauté de voyageurs.
                                </p>
                            </div>
                            @endif

                            <!-- DETAILS CARD -->
                            <div class="details-card">
                                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr class="detail-row">
                                        <td class="detail-label">Référence</td>
                                        <td class="detail-value">#MOR-RSV-{{ $reservation->id }}</td>
                                    </tr>
                                    @if($reservation->hotel)
                                    <tr class="detail-row">
                                        <td class="detail-label">Hôtel</td>
                                        <td class="detail-value">{{ $reservation->hotel->name }}</td>
                                    </tr>
                                    @endif
                                    @if($reservation->room)
                                    <tr class="detail-row">
                                        <td class="detail-label">Chambre</td>
                                        <td class="detail-value">
                                            Chambre {{ $reservation->room->room_number }}
                                            @if($reservation->room->type)
                                                ({{ $reservation->room->type }})
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($reservation->check_in)
                                    <tr class="detail-row">
                                        <td class="detail-label">Arrivée (Check-in)</td>
                                        <td class="detail-value">{{ \Carbon\Carbon::parse($reservation->check_in)->locale('fr')->isoFormat('D MMMM YYYY') }}</td>
                                    </tr>
                                    @endif
                                    @if($reservation->check_out)
                                    <tr class="detail-row">
                                        <td class="detail-label">Départ (Check-out)</td>
                                        <td class="detail-value">{{ \Carbon\Carbon::parse($reservation->check_out)->locale('fr')->isoFormat('D MMMM YYYY') }}</td>
                                    </tr>
                                    @endif
                                    @if($reservation->total_price)
                                    <tr class="detail-row">
                                        <td class="detail-label">Montant total</td>
                                        <td class="detail-value">{{ number_format($reservation->total_price, 2) }} MAD</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- ACTION BUTTON -->
                            <div class="action-container">
                                <a href="{{ $actionUrl }}" class="btn-action {{ $reservation->status === 'checked_out' ? 'btn-accent' : '' }}">
                                    {{ $actionText }}
                                </a>
                            </div>

                            @if($reservation->status === 'checked_out')
                            <p style="font-size: 13px; color: #64748B; text-align: center; margin: 0 0 10px;">
                                Si le bouton ci-dessus ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :<br>
                                <a href="{{ $actionUrl }}" style="color: #0F172A; word-break: break-all;">{{ $actionUrl }}</a>
                            </p>
                            @endif
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td class="footer">
                            <div class="footer-team">L'équipe HotelBooking</div>
                            <p class="footer-note">
                                Cet email vous a été envoyé automatiquement suite à une mise à jour de votre réservation.<br>
                                © {{ date('Y') }} HotelBooking. Tous droits réservés.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
