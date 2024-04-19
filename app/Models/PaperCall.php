<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperCall extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_at',
        'end_at',
        'conference_id',
    ];
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}
