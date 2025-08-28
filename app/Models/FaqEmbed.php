<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqEmbed extends Model
{
    protected $table = 'faq_embeddings';
    protected $fillable = [
        'question',
        'answer',
        'embedding',
    ];
}
