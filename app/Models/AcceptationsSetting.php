<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptationsSetting extends Model
{ 
    use HasFactory;
    protected $table = 'acceptations_setting'; // Ensure table name matches migration

    protected $fillable = [
        'conference_id',
        'oral_presentations',
        'poster',
        'waiting_list',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
