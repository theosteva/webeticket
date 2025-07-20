<table>
    <thead>
        <tr>
            <th>Statistik</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Tiket Masuk</td>
            <td>{{ $totalTiket }}</td>
        </tr>
        <tr>
            <td>Ticket Diterima</td>
            <td>{{ $tiketDiterima }}</td>
        </tr>
        <tr>
            <td>Tiket Dalam Proses</td>
            <td>{{ $tiketDalamProses }}</td>
        </tr>
        <tr>
            <td>Tiket Selesai</td>
            <td>{{ $tiketSelesai }}</td>
        </tr>
        <tr>
            <td>Tiket Belum Di-assign</td>
            <td>{{ $tiketBelumAssign }}</td>
        </tr>
        <tr>
            <td>Tiket Melebihi SLA</td>
            <td>{{ $overdueTickets }}</td>
        </tr>
        <tr>
            <td>Tiket Ditutup</td>
            <td>{{ $tiketDitutup }}</td>
        </tr>
    </tbody>
</table> 