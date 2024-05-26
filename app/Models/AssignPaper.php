<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignPaper extends Model
{
    use HasFactory;
    protected $table = 'assign_paper';

    protected $fillable = [
    'user_id',
            'paper_id',
            'answers',
            'finalDecision',
            'isEligible',
            'comments' ,
            'confidentialRemarks',];

    public function conference()
        {
            return $this->belongsTo(Conference::class, 'conference_id');
        }
        
    public function paper()
        {
            return $this->belongsTo(Paper::class, 'paper_id');
        }
        
    public function user()
        {
            return $this->belongsTo(User::class, 'userId');
        }
        
}
