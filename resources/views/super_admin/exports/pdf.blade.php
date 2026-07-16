<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }} - HotelBooking Report</title>
    <style>
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #334155;
            margin: 0;
            padding: 0;
        }
        
        .header { 
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .logo-text { 
            font-size: 26px; 
            font-weight: bold; 
            color: #0F172A; 
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-accent { color: #1E3A8A; }
        .brand-dot { color: #D4AF37; font-size: 32px; line-height: 0.6; }
        .subtitle { 
            font-size: 9px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            color: #64748b; 
            margin-top: 4px;
        }
        .report-title { 
            font-size: 16px; 
            font-weight: bold; 
            margin-top: 12px; 
            color: #1E3A8A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .timestamp { 
            font-size: 9px; 
            color: #94a3b8; 
            text-align: right;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            background-color: #1E3A8A; 
            color: #ffffff; 
            padding: 10px 8px; 
            text-align: left; 
            font-size: 10px; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 9px 8px; 
            font-size: 10px; 
            color: #475569;
        }
        tr:nth-child(even) { background-color: #F8FAFC; }
        tr:hover { background-color: #EFF6FF; }
        
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #1E3A8A 0%, #D4AF37 100%);
            margin-bottom: 24px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="accent-bar"></div>
    <div class="header">
        <div>
            <div class="logo-text">Hotel<span class="brand-accent">Booking</span></div>
            <div class="subtitle">Premium Hotel Management System</div>
            <div class="report-title">{{ $title }} Report</div>
        </div>
        <div class="timestamp">
            Generated:<br>{{ now()->format('D, M j, Y') }}<br>{{ now()->format('H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} HotelBooking Management System. This is an automatically generated system report. Confidential.
    </div>
</body>
</html>
