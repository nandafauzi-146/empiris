<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pesan' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pesan.required' => 'Pesan balasan wajib diisi.',
            'pesan.max' => 'Pesan balasan tidak boleh lebih dari 2000 karakter.',
        ];
    }
}
