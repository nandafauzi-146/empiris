<aside class="assistant-widget" aria-label="Asisten Empirise" data-assistant-widget>
    <section class="assistant-panel hidden" id="assistant-panel" aria-label="Percakapan dengan Asisten Empirise">
        <header class="assistant-header">
            <div class="assistant-header-info">
                <img src="{{ asset('images/figma/icon-assistant.svg') }}" alt="" aria-hidden="true" class="assistant-header-icon">
                <div>
                    <h2 class="assistant-title">Asisten AI Empirise</h2>
                    <p class="assistant-subtitle">Siap membantu Anda</p>
                </div>
            </div>
            <button class="assistant-close" type="button" aria-label="Tutup asisten" data-chat-close>
                <img src="{{ asset('images/figma/icon-close.svg') }}" alt="" aria-hidden="true">
            </button>
        </header>

        <div class="assistant-body" id="assistant-body" aria-live="polite">
            {{-- Pesan selamat datang --}}
            <div class="assistant-bubble assistant-bubble-ai">
                <span class="assistant-bubble-label">Asisten AI</span>
                <p>Halo! Saya Asisten AI Empirise Jalan-Jalan. Saya siap membantu Anda memahami layanan dan proses konsultasi kami. Apa yang bisa saya bantu?</p>
            </div>

            {{-- Saran pertanyaan awal --}}
            <div class="assistant-suggestions" id="assistant-suggestions" aria-label="Pertanyaan yang sering ditanyakan">
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Apa saja layanan Empirise?</button>
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Bagaimana proses konsultasi?</button>
                <button class="assistant-suggestion" type="button" data-chat-suggestion>Cara memulai konsultasi?</button>
            </div>
        </div>

        {{-- Typing indicator --}}
        <div class="assistant-typing hidden" id="assistant-typing" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>

        <form class="assistant-form" data-chat-form>
            @csrf
            <label class="sr-only" for="assistant-question">Tulis pertanyaan</label>
            <input
                class="assistant-input"
                id="assistant-question"
                type="text"
                placeholder="Tulis pertanyaan..."
                autocomplete="off"
                maxlength="500"
                required
                data-chat-input
            >
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
