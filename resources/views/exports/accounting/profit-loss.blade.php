<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; color: #1a202c; }
        .header p { margin: 5px 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f7fafc; color: #4a5568; font-weight: bold; text-transform: uppercase; font-size: 10px; padding: 10px; border-bottom: 2px solid #edf2f7; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .section-header { background-color: #ebf4ff; color: #2b6cb0; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #f7fafc; font-weight: bold; }
        .net-profit-row { background-color: #4c51bf; color: white; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        @if($branch)
            <p>Cabang: {{ $branch->name }}</p>
        @endif
        <h1>Laporan Laba Rugi</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Revenue -->
            <tr class="section-header"><td colspan="3">I. Pendapatan (Revenue)</td></tr>
            @foreach($reportData['revenues'] as $revenue)
                <tr>
                    <td>{{ $revenue['code'] }}</td>
                    <td>{{ $revenue['name'] }}</td>
                    <td class="text-right">{{ number_format($revenue['total'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Pendapatan</td>
                <td class="text-right">{{ number_format($reportData['total_revenue'], 2) }}</td>
            </tr>

            <!-- COGS -->
            <tr class="section-header"><td colspan="3">II. Harga Pokok Penjualan (COGS)</td></tr>
            @foreach($reportData['cogs'] as $cog)
                <tr>
                    <td>{{ $cog['code'] }}</td>
                    <td>{{ $cog['name'] }}</td>
                    <td class="text-right">({{ number_format($cog['total'], 2) }})</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Harga Pokok Penjualan</td>
                <td class="text-right">({{ number_format($reportData['total_cogs'], 2) }})</td>
            </tr>
            <tr class="total-row" style="background-color: #fffaf0; color: #9c4221;">
                <td colspan="2" class="text-right">Laba Kotor (Gross Profit)</td>
                <td class="text-right">{{ number_format($reportData['gross_profit'], 2) }}</td>
            </tr>

            <!-- Expenses -->
            <tr class="section-header"><td colspan="3">III. Biaya Operasional (Expenses)</td></tr>
            @foreach($reportData['expenses'] as $expense)
                <tr>
                    <td>{{ $expense['code'] }}</td>
                    <td>{{ $expense['name'] }}</td>
                    <td class="text-right">({{ number_format($expense['total'], 2) }})</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Biaya</td>
                <td class="text-right">({{ number_format($reportData['total_expenses'], 2) }})</td>
            </tr>

            <!-- Net Profit -->
            <tr class="net-profit-row">
                <td colspan="2" class="text-right">LABA / (RUGI) BERSIH</td>
                <td class="text-right">{{ number_format($reportData['net_profit'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
