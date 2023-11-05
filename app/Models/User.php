<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User model Class.
 *
 * @property int chat_id
 * @property string first_name
 * @property string | null last_name
 * @property string | null username
 * @property string | null access_token
 * @property string | null refresh_token
 */
class User extends Authenticatable
{
    protected $primaryKey = 'chat_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'first_name',
        'last_name',
        'username',
        'access_token',
        'refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'chat_id' => 'int'
    ];
}
