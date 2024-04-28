<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
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
 public function subtopics()
    {
        return $this->hasMany(Subtopic::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}
