<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'address',
        'job_title',
        'edu',
        'national_id',
        'dob',
        'avatar',
        'phone_verified_at',
        'gender',
        'sms_notification',
        'is_active',
        'level',
        'lang',
        'firebase_token',
        'google_id',
        'facebook_id',
        'remember_token'
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
        'name' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'job_title' => 'string',
        'edu' => 'string',
        'national_id' => 'string',
        'dob' => 'date',
        'avatar' => 'string',
        'phone_verified_at' => 'datetime',
        'gender' => 'string',
        'sms_notification' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'boolean',
        'lang' => 'string',
        'firebase_token' => 'string',
        'google_id' => 'string',
        'facebook_id' => 'string',
        'remember_token' => 'string'
    ];

    public static $rules = [
        'name' => 'required|string',
        // 'email' => 'required|string|unique:users',
        // 'password' => 'required|string',
        'phone' => 'required|string|unique:users',
        // 'address' => 'nullable|string'
    ];

    public static $rulesPrivet = [
        'name' => 'required|string',
        'email' => 'required|string|unique:users',
        'password' => 'required|string',
        'phone' => 'required|string|unique:users',
        'address' => 'nullable|string'
    ];

}
