<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;
    protected $fillable = [
        'userID',
        'acronym',
        'webpage',
        'address',
        'country',
        'start_at',
        'end_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function paperCalls()
    {
        return $this->hasMany(PaperCall::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
