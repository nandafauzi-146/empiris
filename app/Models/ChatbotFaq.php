<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kata_kunci', 'pertanyaan', 'jawaban', 'aktif'])]
class ChatbotFaq extends Model
{
    protected $table = 'faq_chatbot';

    protected $primaryKey = 'id_faq';

    protected function casts(): array
    {
        return ['kata_kunci' => 'array', 'aktif' => 'boolean'];
    }
}
