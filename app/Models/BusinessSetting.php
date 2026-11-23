<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kunci', 'nilai'])]
class BusinessSetting extends Model
{
    protected $table = 'pengaturan_bisnis';

    protected $primaryKey = 'kunci';

    public $incrementing = false;

    protected $keyType = 'string';
}
