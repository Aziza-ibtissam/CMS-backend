<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'point',
        'coefficient',
        'form_id',
    ];
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
