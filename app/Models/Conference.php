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
        'start_at',
        'end_at',
        'paper_subm_due_date',
        'logo',
        'userID',
       'register_due_date',
       'acceptation_notification',
       'camera_ready_paper',
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
        return $this->hasOne(PaperCall::class);
    }

    public function topics()
    {
        return $this->hasOne(Topic::class,);
    }

    

    public function forms()
    {
        return $this->hasOne(Form::class,);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }
}
