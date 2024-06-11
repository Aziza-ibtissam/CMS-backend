<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'day',
        'date',
        'start_time',
        'end_time',
        'conference_id',
        'session_number',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class); // Assuming the related model is Conference
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
