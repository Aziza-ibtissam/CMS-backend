<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'score',
        'track_id',
    ];
    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
