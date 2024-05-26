<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $fillable = [
        'conference_id',
        'finalDecisionCoefficient',
        'confidentialRemarksCoefficient',
        'eligibleCoefficient'
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
