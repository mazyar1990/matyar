<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corpus extends Model
{
    protected $table = 'corpora';

    protected $fillable = [
        'name', 
        'source_lang', 
        'target_lang'
    ];

    //
}
