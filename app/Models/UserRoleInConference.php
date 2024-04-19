<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleInConference extends Model
{
    use HasFactory;
    <?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoleInConference extends Model
{
    protected $table = 'user_role_in_conference';

    protected $fillable = [
        'user_id',
        'role_id',
        'conference_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}

}
