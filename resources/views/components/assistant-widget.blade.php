<aside class="assistant-widget" aria-label="Asisten Empirise" data-assistant-widget>
    <section class="assistant-panel hidden" id="assistant-panel" aria-label="Percakapan dengan Asisten Empirise">
        <header class="assistant-header">
            <h2 class="assistant-title">
                <img src="{{ asset('images/figma/icon-assistant.svg') }}" alt="" aria-hidden="true">
                Asisten Empirise
            </h2>
            <button class="assistant-close" type="button" aria-label="Tutup asisten" data-chat-close>
                <img src="{{ asset('images/figma/icon-close.svg') }}" alt="" aria-hidden="true">
            </button>
        </header>

        <div class="assistant-body">
            <p class="assistant-message">
                Halo! Saya asisten Empirise. Tanyakan soal layanan, jadwal, biaya, atau pembayaran.
            </p>
            <div class="assistant-suggestions" aria-label="Pertanyaan yang sering diajukan">
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Berapa biaya?</button>
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Jadwal tersedia?</button>
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Cara bayar</button>
            </div>
        </div>

        <form class="assistant-form" action="https://wa.me/6281234567890" method="get" target="_blank" data-chat-form>
            <label class="sr-only" for="assistant-question">Tulis pertanyaan</label>
            <input class="assistant-input" id="assistant-question" name="text" type="text" placeholder="Tulis pertanyaan..." autocomplete="off" required>
            <button class="assistant-send" type="submit" aria-label="Kirim pertanyaan">
                <img src="{{ asset('images/figma/icon-send.svg') }}" alt="" aria-hidden="true">
            </button>
        </form>
    </section>

    <button
        class="assistant-toggle"
        type="button"
        aria-label="Buka asisten"
        aria-expanded="false"
        aria-controls="assistant-panel"
        data-chat-toggle
    >
        <img src="{{ asset('images/figma/icon-assistant.svg') }}" alt="" aria-hidden="true" data-chat-open-icon>
        <img src="{{ asset('images/figma/icon-chat-toggle.svg') }}" alt="" aria-hidden="true" data-chat-close-icon>
    </button>
</aside>
