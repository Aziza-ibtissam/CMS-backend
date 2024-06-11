<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtopic extends Model
{
    protected $fillable = [
        'name',
        'topic_id',
    ];
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
