<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    protected $fieldSearchable = [
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

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return User::class;
    }
}
