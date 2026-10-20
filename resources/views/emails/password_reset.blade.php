<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectTitle ?? 'Reset Your Password' }}</title>
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
        .heading-section {
            font-size: 20px;
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
        .info-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 28px;
        }
        .info-card-text {
            font-size: 13px;
            color: #64748B;
            line-height: 1.6;
            margin: 0;
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
        .security-note {
            font-size: 13px;
            color: #64748B;
            line-height: 1.5;
            margin: 20px 0 0;
        }
        .url-fallback {
            font-size: 12px;
            color: #94A3B8;
            line-height: 1.5;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
            word-break: break-all;
        }
        .url-fallback a {
            color: #0F172A;
            text-decoration: underline;
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
                            <div class="brand-sub">Account Security</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td class="content">
                            <h2 class="greeting">Hello {{ $clientName }},</h2>

                            <h3 class="heading-section">Reset Your Password</h3>

                            <p class="message-lead">
                                You are receiving this email because we received a password reset request for your HotelBooking account. Click the button below to choose a new password:
                            </p>

                            <!-- ACTION BUTTON -->
                            <div class="action-container">
                                <a href="{{ $resetUrl }}" class="btn-action">
                                    Reset Password
                                </a>
                            </div>

                            <!-- INFO CARD -->
                            <div class="info-card">
                                <p class="info-card-text">
                                    <strong>Important:</strong> This password reset link will expire in <strong>{{ $expireMinutes }} minutes</strong>. For your security, this link can only be used once.
                                </p>
                            </div>

                            <p class="security-note">
                                If you did not request a password reset, no further action is required and your account remains safe and secure.
                            </p>

                            <!-- URL FALLBACK -->
                            <div class="url-fallback">
                                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
                            </div>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td class="footer">
                            <div class="footer-team">The HotelBooking Team</div>
                            <p class="footer-note">
                                This is an automated email sent to protect your account.<br>
                                © {{ date('Y') }} HotelBooking. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
