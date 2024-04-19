<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'conference_id',
    ];
    
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
