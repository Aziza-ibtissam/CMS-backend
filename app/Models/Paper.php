<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;
    protected $fillable = [
        'paperFile',
        'paperTitle',
        'abstract',
        'keywords',
        'submitted_at',
        'status',
        'conference_id',
        'user_id',
        'mark',
        'finalVersionFile'

    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paper) {
            $latestPaper = Paper::orderBy('id', 'desc')->first();
            $latestId = $latestPaper ? $latestPaper->id : 0;
            $newId = str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);
            $paper->custom_id = 'P' . $newId;
        });
    }
    
    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'assign_paper')
            ->withPivot(['answers', 'finalDecision', 'isEligible', 'comments', 'confidentialRemarks']);
    }
    public function conference()
    {
        return $this->belongsTo(Conference::class); // Define the relationship as belongsTo
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
