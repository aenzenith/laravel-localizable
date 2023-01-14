<?php

namespace Aenzenith\LaravelLocalizable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localization extends Model
{
    use HasFactory;

    protected $table = 'localizations';

    protected $fillable = [
        'model_type',
        'model_id',
        'locale',
        'field',
        'value',
    ];
}
