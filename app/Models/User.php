<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\EmailVerificationNotification;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'username',
        'email',
        'password',
        'phoneNumber',
        'country',
        'affiliation',
        'dateOfBirth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
       $this->notify(new EmailVerificationNotification);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

}
