<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $fillable = ['sessionKeywords', 'sessionPaper',  'conference_id', 'conference_schedule_id'];
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
    
    public function papers() {
        return $this->hasMany(Paper::class);
    }
    public function conferenceSchedule()
    {
        return $this->belongsTo(ConferenceSchedule::class);
    }
    
}
