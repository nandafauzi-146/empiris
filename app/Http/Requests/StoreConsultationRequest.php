<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'layanan' => [
                'required',
                Rule::in([
                    'Tour & Wisata',
                    'Event & Gathering',
                    'Outbound',
                    'Rental Armada',
                    'Team Building',
                    'Kunjungan Industri & Edukasi',
                    'Pelatihan & Pengembangan SDM',
                    'Hiburan & Awarding',
                ]),
            ],
            'tipe_klien' => ['nullable', 'string', Rule::in(['korporat', 'kampus', 'instansi', 'umum'])],
            'hasil_diinginkan' => ['nullable', 'string', 'max:2000'],
            'tujuan' => ['nullable', 'string', 'max:255'],
            'jumlah_peserta' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'durasi_hari' => ['nullable', 'integer', 'min:1', 'max:365'],
            'tanggal_rencana' => ['nullable', 'date'],
            'minat' => ['nullable', 'string', 'max:1000'],
            'cerita_kebutuhan' => ['nullable', 'string', 'max:1500'],
            'jenis_kendaraan' => [
                'exclude_unless:layanan,Rental Armada',
                'required',
                'string',
                Rule::in(['Mobil', 'Minibus', 'Bus', 'Belum menentukan']),
            ],
            'lokasi_jemput' => [
                'exclude_unless:layanan,Rental Armada',
                'required',
                'string',
                'max:255',
            ],
            'jam_jemput' => [
                'exclude_unless:layanan,Rental Armada',
                'nullable',
                'date_format:H:i',
            ],
            'tanggal_kembali' => [
                'exclude_unless:layanan,Rental Armada',
                'nullable',
                'date',
                Rule::when($this->filled('tanggal_rencana'), ['after_or_equal:tanggal_rencana']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'layanan.required' => 'Pilih kebutuhan kegiatan Anda.',
            'layanan.in' => 'Pilih salah satu layanan yang tersedia.',
            'jumlah_peserta.integer' => 'Jumlah peserta harus berupa angka.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang.',
            'durasi_hari.integer' => 'Durasi harus berupa angka.',
            'durasi_hari.min' => 'Durasi minimal 1 hari.',
            'tanggal_rencana.date' => 'Masukkan tanggal rencana yang valid.',
            'cerita_kebutuhan.max' => 'Cerita kebutuhan tidak boleh lebih dari 1500 karakter.',
            'jenis_kendaraan.required' => 'Pilih jenis kendaraan atau pilih belum menentukan.',
            'jenis_kendaraan.in' => 'Pilih jenis kendaraan yang tersedia.',
            'lokasi_jemput.required' => 'Lokasi penjemputan wajib diisi untuk rental armada.',
            'lokasi_jemput.max' => 'Lokasi penjemputan tidak boleh lebih dari 255 karakter.',
            'jam_jemput.date_format' => 'Waktu penjemputan harus menggunakan format jam yang valid.',
            'tanggal_kembali.date' => 'Masukkan tanggal kembali yang valid.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali harus pada atau setelah tanggal rencana.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama',
            'email' => 'email',
            'telepon' => 'nomor telepon',
            'instansi' => 'instansi',
            'layanan' => 'layanan',
            'tujuan' => 'destinasi',
            'jumlah_peserta' => 'jumlah peserta',
            'durasi_hari' => 'durasi',
            'tanggal_rencana' => 'tanggal rencana',
            'minat' => 'minat',
            'cerita_kebutuhan' => 'cerita kebutuhan',
            'jenis_kendaraan' => 'jenis kendaraan',
            'lokasi_jemput' => 'lokasi penjemputan',
            'jam_jemput' => 'waktu penjemputan',
            'tanggal_kembali' => 'tanggal kembali',
        ];
    }
}
