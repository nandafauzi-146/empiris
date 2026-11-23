@extends('layouts.admin')

@section('title', 'Chatbot AI — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">ASISTEN DIGITAL</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Chatbot AI</h1>
        <p class="mt-2 text-copy">Kelola FAQ, pantau ringkasan log, dan atur parameter rekomendasi.</p>
    </header>
    <nav class="mb-6 flex flex-wrap gap-2" aria-label="Bagian chatbot">
        @foreach (['faq' => 'FAQ Chatbot', 'log' => 'Log percakapan', 'rekomendasi' => 'Rekomendasi'] as $key => $label)
            <a class="rounded-full border px-4 py-2 text-sm font-semibold {{ $tab === $key ? 'border-ink bg-ink text-white' : 'border-line text-copy' }}" href="{{ route('admin.chatbot.index', ['tab' => $key]) }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>

    @if ($tab === 'faq')
        <details class="mb-6 rounded-3xl border border-line p-5">
            <summary class="cursor-pointer font-bold">Tambah FAQ</summary>
            <form class="mt-4 grid gap-3" action="{{ route('admin.chatbot.faq.store') }}" method="POST">
                @csrf
                <label class="text-sm font-semibold">Kata kunci (pisahkan dengan koma)<input class="mt-1 w-full rounded-xl border-line" name="kata_kunci" required></label>
                <label class="text-sm font-semibold">Pertanyaan<input class="mt-1 w-full rounded-xl border-line" name="pertanyaan" required></label>
                <label class="text-sm font-semibold">Jawaban<textarea class="mt-1 w-full rounded-xl border-line" name="jawaban" rows="3" required></textarea></label>
                <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="aktif" value="1" checked> Aktif</label>
                <button class="w-fit rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-white" type="submit">Tambah FAQ</button>
            </form>
        </details>
        <div class="space-y-4">
            @forelse ($faqs as $faq)
                <details class="rounded-3xl border border-line p-5">
                    <summary class="cursor-pointer font-bold">{{ $faq->pertanyaan }} · {{ $faq->aktif ? 'Aktif' : 'Nonaktif' }}</summary>
                    <form class="mt-4 grid gap-3" action="{{ route('admin.chatbot.faq.update', $faq) }}" method="POST">
                        @csrf @method('PATCH')
                        <label class="text-sm font-semibold">Kata kunci<input class="mt-1 w-full rounded-xl border-line" name="kata_kunci" value="{{ implode(', ', $faq->kata_kunci) }}" required></label>
                        <label class="text-sm font-semibold">Pertanyaan<input class="mt-1 w-full rounded-xl border-line" name="pertanyaan" value="{{ $faq->pertanyaan }}" required></label>
                        <label class="text-sm font-semibold">Jawaban<textarea class="mt-1 w-full rounded-xl border-line" name="jawaban" rows="3" required>{{ $faq->jawaban }}</textarea></label>
                        <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="aktif" value="1" @checked($faq->aktif)> Aktif</label>
                        <button class="w-fit rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white" type="submit">Simpan</button>
                    </form>
                    <form class="mt-3" action="{{ route('admin.chatbot.faq.destroy', $faq) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-sm font-semibold text-red-700 underline" type="submit">Nonaktifkan FAQ</button>
                    </form>
                </details>
            @empty
                <p class="rounded-3xl border border-dashed border-line p-8 text-center text-copy">Belum ada FAQ chatbot.</p>
            @endforelse
            {{ $faqs->links() }}
        </div>
    @elseif ($tab === 'log')
        <form class="mb-4" method="GET">
            <input type="hidden" name="tab" value="log">
            <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="belum_terjawab" value="1" @checked(request()->boolean('belum_terjawab'))> Hanya belum terjawab</label>
            <button class="mt-2 rounded-full border border-line px-4 py-2 text-sm font-semibold" type="submit">Filter log</button>
        </form>
        <div class="overflow-x-auto rounded-3xl border border-line">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-copy"><tr><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">Mode</th><th class="px-4 py-3">Ringkasan pertanyaan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Latensi</th></tr></thead>
                <tbody class="divide-y divide-line">
                    @forelse ($logs as $log)
                        <tr><td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ ucfirst($log->mode) }}</td><td class="max-w-md px-4 py-3">{{ $log->ringkasan_pertanyaan }}</td><td class="px-4 py-3">{{ $log->terjawab ? 'Terjawab' : 'Belum terjawab' }} · {{ $log->status }}</td><td class="px-4 py-3">{{ $log->latensi_ms ?? '—' }} ms</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-5 text-copy">Belum ada log percakapan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    @else
        <section class="rounded-3xl border border-line p-5">
            <h2 class="text-xl font-bold">Parameter rekomendasi</h2>
            <p class="mt-2 text-sm text-copy">Nilai disimpan sebagai konfigurasi bisnis. Perubahan belum mengeksekusi uji rekomendasi.</p>
            <form class="mt-5 flex flex-wrap items-end gap-4" action="{{ route('admin.chatbot.settings') }}" method="POST">
                @csrf @method('PATCH')
                <label class="text-sm font-semibold">Ambang skor (0–1)<input class="mt-1 block rounded-xl border-line" type="number" step="0.01" min="0" max="1" name="ambang_skor" value="{{ $settings['ambang_skor'] }}" required></label>
                <label class="text-sm font-semibold">Jumlah hasil (1–20)<input class="mt-1 block rounded-xl border-line" type="number" min="1" max="20" name="jumlah_hasil" value="{{ $settings['jumlah_hasil'] }}" required></label>
                <button class="rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-white" type="submit">Simpan parameter</button>
            </form>
        </section>
        <section class="mt-6 rounded-3xl border border-line p-5">
            <h2 class="text-xl font-bold">Log rekomendasi terbaru</h2>
            <div class="mt-3 divide-y divide-line">
                @forelse ($recommendations as $recommendation)
                    <div class="py-3"><p class="text-sm font-semibold">{{ $recommendation->created_at->format('d/m/Y H:i') }} · {{ $recommendation->latensi_ms ?? '—' }} ms</p><p class="mt-1 text-xs text-copy">Preferensi: {{ json_encode($recommendation->preferensi_json, JSON_UNESCAPED_UNICODE) }}</p></div>
                @empty
                    <p class="py-4 text-sm text-copy">Belum ada log rekomendasi.</p>
                @endforelse
            </div>
        </section>
    @endif
@endsection
