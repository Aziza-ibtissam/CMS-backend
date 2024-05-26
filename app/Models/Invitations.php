<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitations extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'conference_id',
        'firstName',
        'lastName',
        'affiliation',
        'invitationStatus',
        'reviewerTopic'
    ];
}
