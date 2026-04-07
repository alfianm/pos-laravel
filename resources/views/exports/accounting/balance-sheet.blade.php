<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - as of {{ $asOfDate }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; color: #1a202c; }
        .header p { margin: 5px 0; color: #718096; }
        .container { display: table; width: 100%; }
        .column { display: table-cell; width: 50%; padding: 0 10px; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f7fafc; color: #4a5568; font-weight: bold; text-transform: uppercase; font-size: 10px; padding: 10px; border-bottom: 2px solid #edf2f7; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .section-header { background-color: #f7fafc; color: #1a202c; font-weight: bold; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #cbd5e0; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #edf2f7; font-weight: bold; }
        .grand-total { background-color: #4c51bf; color: white; font-weight: bold; padding: 15px; margin-top: 10px; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; color: #a0aec0; }
        .unbalanced { color: #e53e3e; border: 1px solid #feb2b2; background-color: #fff5f5; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        @if($branch)
            <p>Cabang: {{ $branch->name }}</p>
        @endif
        <h1>Laporan Neraca</h1>
        <p>Per Tanggal: {{ \Carbon\Carbon::parse($asOfDate)->format('d/m/Y') }}</p>
    </div>

    @php
        $totalPassiva = $reportData['total_liabilities'] + $reportData['total_equity'];
        $diff = abs($reportData['total_assets'] - $totalPassiva);
    @endphp

    @if($diff > 0.01)
        <div class="unbalanced">
            <strong>Peringatan!</strong> Neraca tidak seimbang. Selisih: Rp {{ number_format($diff, 2) }}
        </div>
    @endif

    <div class="container">
        <!-- Aktiva -->
        <div class="column">
            <table>
                <thead>
                    <tr><th colspan="2" class="section-header">AKTIVA (ASSETS)</th></tr>
                </thead>
                <tbody>
                    @foreach($reportData['assets'] as $asset)
                        <tr>
                            <td>{{ $asset['name'] }}<br><small style="color: #a0aec0;">{{ $asset['code'] }}</small></td>
                            <td class="text-right">{{ number_format($asset['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="text-right">TOTAL AKTIVA</td>
                        <td class="text-right">{{ number_format($reportData['total_assets'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Pasiva -->
        <div class="column">
            <!-- Kewajiban -->
            <table>
                <thead>
                    <tr><th colspan="2" class="section-header">KEWAJIBAN (LIABILITIES)</th></tr>
                </thead>
                <tbody>
                    @foreach($reportData['liabilities'] as $liability)
                        <tr>
                            <td>{{ $liability['name'] }}<br><small style="color: #a0aec0;">{{ $liability['code'] }}</small></td>
                            <td class="text-right">{{ number_format($liability['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="text-right">TOTAL KEWAJIBAN</td>
                        <td class="text-right">{{ number_format($reportData['total_liabilities'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Modal -->
            <table>
                <thead>
                    <tr><th colspan="2" class="section-header">MODAL (EQUITY)</th></tr>
                </thead>
                <tbody>
                    @foreach($reportData['equity'] as $eq)
                        <tr>
                            <td>{{ $eq['name'] }}<br><small style="color: #a0aec0;">{{ $eq['code'] }}</small></td>
                            <td class="text-right">{{ number_format($eq['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="text-right">TOTAL MODAL</td>
                        <td class="text-right">{{ number_format($reportData['total_equity'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="grand-total">
                <table style="margin: 0; color: inherit; background: transparent;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 0;">TOTAL PASIVA</td>
                        <td style="border: none; padding: 0;" class="text-right">{{ number_format($totalPassiva, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
