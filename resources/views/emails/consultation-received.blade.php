<!DOCTYPE html>
<html lang="id">
    <body>
        @if ($forAdmin)
            <h1>Konsultasi baru dari {{ $consultation->nama }}</h1>
            <p>Nomor konsultasi: <strong>{{ $consultation->nomor_konsultasi }}</strong></p>
            <p>Email: {{ $consultation->email }}</p>
            <p>Telepon: {{ $consultation->telepon }}</p>
            @if ($consultation->instansi)
                <p>Instansi: {{ $consultation->instansi }}</p>
            @endif
            <p>Layanan: {{ $consultation->layanan }}</p>
            @if ($consultation->tujuan)
                <p>Destinasi: {{ $consultation->tujuan }}</p>
            @endif
            @if ($consultation->jumlah_peserta)
                <p>Jumlah peserta: {{ $consultation->jumlah_peserta }}</p>
            @endif
            @if ($consultation->durasi_hari)
                <p>Durasi: {{ $consultation->durasi_hari }} hari</p>
            @endif
            @if ($consultation->tanggal_rencana)
                <p>Tanggal rencana: {{ $consultation->tanggal_rencana->format('d/m/Y') }}</p>
            @endif
            @if ($consultation->layanan === 'Rental Armada')
                <p>Jenis kendaraan yang diminta: {{ $consultation->jenis_kendaraan }}</p>
                <p>Lokasi penjemputan: {{ $consultation->lokasi_jemput }}</p>
                @if ($consultation->jam_jemput)
                    <p>Jam penjemputan: {{ substr($consultation->jam_jemput, 0, 5) }}</p>
                @endif
                @if ($consultation->tanggal_kembali)
                    <p>Tanggal kembali: {{ $consultation->tanggal_kembali->format('d/m/Y') }}</p>
                @endif
                <p>Permintaan armada ini belum menjadi reservasi. Konfirmasi ketersediaan dan penawaran akan diberikan oleh admin.</p>
            @endif
            @if ($consultation->minat)
                <p>Minat: {{ $consultation->minat }}</p>
            @endif
            @if ($consultation->cerita_kebutuhan)
                <p>Catatan kebutuhan:</p>
                <p>{{ $consultation->cerita_kebutuhan }}</p>
            @endif
        @else
            <h1>Terima kasih, {{ $consultation->nama }}.</h1>
            <p>Konsultasi Anda sudah kami terima.</p>
            <p>Nomor konsultasi: <strong>{{ $consultation->nomor_konsultasi }}</strong></p>
            <p>Tim Empirise akan meninjau kebutuhan Anda dan menghubungi melalui email.</p>
            @if ($consultation->layanan === 'Rental Armada')
                <p>Permintaan rental Anda belum menjadi reservasi terkonfirmasi. Tim kami akan mengonfirmasi ketersediaan kendaraan sebelum mengirim penawaran.</p>
                <p>Jenis kendaraan: {{ $consultation->jenis_kendaraan }}</p>
                <p>Lokasi penjemputan: {{ $consultation->lokasi_jemput }}</p>
                @if ($consultation->jam_jemput)
                    <p>Jam penjemputan: {{ substr($consultation->jam_jemput, 0, 5) }}</p>
                @endif
                @if ($consultation->tanggal_kembali)
                    <p>Tanggal kembali: {{ $consultation->tanggal_kembali->format('d/m/Y') }}</p>
                @endif
            @endif
        @endif

        <p><a href="{{ $homeUrl }}">Kunjungi website Empirise Jalan-Jalan</a></p>
    </body>
</html>
