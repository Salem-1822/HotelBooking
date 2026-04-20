<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }} - HOTELIA Report</title>
    <style>
        /* UPDATE PDF LAYOUT */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #334155;
            margin: 0;
            padding: 0;
        }
        
        /* UPDATE PDF HEADER BRANDING */
        .header { 
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-text { 
            font-size: 28px; 
            font-weight: bold; 
            color: #0f172a; 
            margin: 0;
        }
        .brand-accent { color: #f97316; }
        .subtitle { 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            color: #64748b; 
            margin-top: 4px;
        }
        .report-title { 
            font-size: 18px; 
            font-weight: bold; 
            margin-top: 15px; 
            color: #f97316;
        }
        .timestamp { 
            font-size: 9px; 
            color: #94a3b8; 
            text-align: right;
            margin-top: -40px;
        }

        /* UPDATE PDF TABLE STYLING */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            background-color: #0f172a; 
            color: #ffffff; 
            padding: 12px 10px; 
            text-align: left; 
            font-size: 11px; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 10px; 
            font-size: 11px; 
            color: #475569;
        }
        tr:nth-child(even) { background-color: #f8fafc; }
        
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-text">HOTEL<span class="brand-accent">IA</span></div>
        <div class="subtitle">Premier Hotel Management System</div>
        <div class="report-title">{{ $title }} Report</div>
        <div class="timestamp">Generated: {{ now()->format('D, M j, Y @ H:i') }}</div>
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
        &copy; {{ date('Y') }} HOTELIA Management. This is an automatically generated system report.
    </div>
</body>
</html>
