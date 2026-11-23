<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationReplyRequest;
use App\Models\Consultation;
use Illuminate\Http\RedirectResponse;

class ConsultationReplyController extends Controller
{
    public function store(StoreConsultationReplyRequest $request, Consultation $consultation): RedirectResponse
    {
        $ownedConsultation = $request->user()
            ->consultations()
            ->findOrFail($consultation->getKey());

        $ownedConsultation->replies()->create([
            'id_pengirim' => $request->user()->getKey(),
            'pesan' => $request->validated('pesan'),
        ]);
        $ownedConsultation->touch();

        return to_route('account.consultations.show', $ownedConsultation)
            ->with('status', 'Pesan Anda sudah dikirim.');
    }
}
