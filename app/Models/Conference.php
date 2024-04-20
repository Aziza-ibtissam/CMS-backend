<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'title',
        'acronym',
        'city',
        'country',
        'webpage',
        'category',
        'form_id',
        'topic_id',
        'paper_call_id',
        'start_at',
        'end_at',
        'paper_subm_date',
        'logo',
        'userID'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }
        
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function paperCalls()
    {
        return $this->hasMany(PaperCall::class,'paper_call_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class,'topic_id');
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function forms()
    {
        return $this->belongsTo(Form::class,'form_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
