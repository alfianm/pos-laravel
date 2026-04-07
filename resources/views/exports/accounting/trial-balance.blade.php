<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trial Balance - {{ $period }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; color: #1a202c; }
        .header p { margin: 5px 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f7fafc; color: #4a5568; font-weight: bold; text-transform: uppercase; font-size: 10px; padding: 10px; border-bottom: 2px solid #edf2f7; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; color: #a0aec0; }
        .total-row { background-color: #f7fafc; font-weight: bold; }
        .balanced-status { margin-top: 10px; padding: 10px; border-radius: 5px; text-align: center; }
        .status-success { background-color: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }
        .status-error { background-color: #fff5f5; color: #9b2c2c; border: 1px solid #fed7d7; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        @if($branch)
            <p>Cabang: {{ $branch->name }}</p>
        @endif
        <h1>Neraca Saldo</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($period.'-01')->format('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trialBalance as $item)
                <tr>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">{{ $item['display_debit'] > 0 ? number_format($item['display_debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $item['display_credit'] > 0 ? number_format($item['display_credit'], 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="balanced-status {{ round($totalDebit, 2) === round($totalCredit, 2) ? 'status-success' : 'status-error' }}">
        {{ round($totalDebit, 2) === round($totalCredit, 2) ? 'Status: Seimbang (Balanced)' : 'Status: Tidak Seimbang (Out of Balance)' }}
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
