<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Flow - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; color: #1a202c; }
        .header p { margin: 5px 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f7fafc; color: #4a5568; font-weight: bold; text-transform: uppercase; font-size: 10px; padding: 10px; border-bottom: 2px solid #edf2f7; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .section-header { background-color: #f7fafc; color: #2d3748; font-weight: bold; font-size: 11px; border-bottom: 2px solid #e2e8f0; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #f7fafc; font-weight: bold; }
        .grand-total { background-color: #1a202c; color: white; font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; color: #a0aec0; }
        .inflow { color: #2f855a; }
        .outflow { color: #c53030; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        @if($branch)
            <p>Cabang: {{ $branch->name }}</p>
        @endif
        <h1>Laporan Arus Kas</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table>
        <tbody>
            <tr class="font-bold">
                <td>SALDO AWAL (Opening Balance)</td>
                <td class="text-right">{{ number_format($reportData['opening_balance'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>Aktivitas dan Akun</th>
                <th class="text-right">Masuk (Inflow)</th>
                <th class="text-right">Keluar (Outflow)</th>
                <th class="text-right">Netto</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['operating' => 'Aktivitas Operasional', 'investing' => 'Aktivitas Investasi', 'financing' => 'Aktivitas Pendanaan'] as $key => $label)
                <tr class="section-header"><td colspan="4">{{ $label }}</td></tr>
                
                @foreach($reportData[$key]['in'] as $in)
                    <tr>
                        <td style="padding-left: 20px;">{{ $in['name'] }}</td>
                        <td class="text-right inflow">{{ number_format($in['total'], 2) }}</td>
                        <td class="text-right" style="color: #cbd5e0;">-</td>
                        <td></td>
                    </tr>
                @endforeach

                @foreach($reportData[$key]['out'] as $out)
                    <tr>
                        <td style="padding-left: 20px;">{{ $out['name'] }}</td>
                        <td class="text-right" style="color: #cbd5e0;">-</td>
                        <td class="text-right outflow">({{ number_format($out['total'], 2) }})</td>
                        <td></td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="3" class="text-right">Total {{ $label }}</td>
                    <td class="text-right">{{ number_format($reportData[$key]['net'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-right">KENAIKAN / (PENURUNAN) KAS BERSIH</td>
                <td class="text-right">{{ number_format($reportData['net_cash_flow'], 2) }}</td>
            </tr>
            <tr class="grand-total" style="background-color: #2b6cb0;">
                <td colspan="3" class="text-right">SALDO AKHIR (Closing Balance)</td>
                <td class="text-right text-lg">{{ number_format($reportData['closing_balance'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
