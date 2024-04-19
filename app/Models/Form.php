<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $fillable = [
        'equation',
        'conference_id',
    ];
    
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}