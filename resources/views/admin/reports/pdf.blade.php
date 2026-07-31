<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Performance Report - {{ $hotel->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .hotel-name {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .report-title {
            font-size: 15px;
            color: #475569;
            margin-top: 4px;
            font-weight: 500;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #334155;
            width: 140px;
        }
        .meta-val {
            color: #475569;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }
        .kpi-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .kpi-grid td {
            width: 33.33%;
            border: 1px solid #e2e8f0;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }
        .kpi-val {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .kpi-name {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -10px;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
        }
        .footer .page-number:after {
            content: counter(page);
        }
        .empty-state {
            text-align: center;
            padding: 60px 0;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
        }
        .empty-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="hotel-name">{{ $hotel->name }}</div>
        <div class="report-title">Hotel Performance & Revenue Report</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Generated on:</td>
            <td class="meta-val">{{ now()->format('F d, Y @ h:i A') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Filtered Scope:</td>
            <td class="meta-val">All Time (Default Period)</td>
        </tr>
        <tr>
            <td class="meta-label">Currency Standard:</td>
            <td class="meta-val">Moroccan Dirham (MAD / DH)</td>
        </tr>
    </table>

    <div class="section-title">Key Performance Indicators</div>

    @if($totalReservations === 0)
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <strong>No Report Data Available</strong>
            <p style="margin: 5px 0 0 0; font-size: 11px;">There are currently no reservations logged for this hotel in the system.</p>
        </div>
    @else
        <table class="kpi-grid">
            <tr>
                <td>
                    <div class="kpi-val">{{ number_format($totalRevenue, 2) }} MAD</div>
                    <div class="kpi-name">Total Revenue</div>
                </td>
                <td>
                    <div class="kpi-val">{{ $totalReservations }}</div>
                    <div class="kpi-name">Total Bookings</div>
                </td>
                <td>
                    <div class="kpi-val">{{ $completedReservations }}</div>
                    <div class="kpi-name">Completed Stays</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="kpi-val">{{ $pendingReservations }}</div>
                    <div class="kpi-name">Pending Bookings</div>
                </td>
                <td>
                    <div class="kpi-val">{{ $cancelledReservations }}</div>
                    <div class="kpi-name">Cancelled Bookings</div>
                </td>
                <td>
                    <div class="kpi-val">{{ $totalCustomers }}</div>
                    <div class="kpi-name">Total Customers</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="footer">
        <span>Hotelia Management System — Page <span class="page-number"></span></span>
    </div>

</body>
</html>
