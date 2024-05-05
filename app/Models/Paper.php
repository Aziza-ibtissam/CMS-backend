<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;
    protected $fillable = [
        'paperFile',
        'paperName',
        'submitted_at',
        'status',
        'emailAuth',
        'conference_id',
        'mark'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
