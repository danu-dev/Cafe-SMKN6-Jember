<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Cafe</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #5c3d2e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #5c3d2e;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 11px;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-box {
            background: #f8f6f0;
            border: 1px solid #e2d9cc;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-box .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #777;
            font-weight: bold;
        }
        .summary-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #5c3d2e;
            margin-top: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #5c3d2e;
            color: #ffffff;
            padding: 6px 8px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0e7ff; color: #3730a3; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN CAFE KANTIN</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td class="summary-box" style="width: 25%;">
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="label">Total Pesanan</div>
                <div class="value">{{ $totalPesanan }}</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="label">Pesanan Selesai</div>
                <div class="value" style="color: #065f46;">{{ $totalSelesai }}</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="label">Pesanan Batal</div>
                <div class="value" style="color: #991b1b;">{{ $totalBatal }}</div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 12px; color: #5c3d2e; margin-bottom: 6px;">Rincian Transaksi</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Kode</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 18%;">Pemesan</th>
                <th style="width: 25%;">Item</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">#{{ $order->kode_pesanan }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $order->user->name ?? '-' }}</strong>
                        @if($order->user && $order->user->kelas)
                            <div style="font-size: 8px; color: #777;">Kelas {{ $order->user->kelas }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $order->items->map(fn($i) => ($i->menu->nama ?? 'Menu') . ' (' . $i->jumlah . 'x)')->join(', ') }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $order->status === 'selesai' ? 'badge-success' : ($order->status === 'dibatalkan' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #888;">
                        Tidak ada data transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->isoFormat('dddd, D MMMM Y HH:mm') }} WIB
    </div>
</body>
</html>
