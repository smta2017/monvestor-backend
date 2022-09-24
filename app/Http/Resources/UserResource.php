<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
            'phone' => $this->phone,
            'address' => $this->address,
            'national_id' => $this->national_id,
            'avatar' => $this->avatar,
            'is_active' => $this->is_active,
            'level' => $this->level,
            'lang' => $this->lang,
            'firebase_token' => $this->firebase_token,
            'google_id' => $this->google_id,
            'facebook_id' => $this->facebook_id,
        ];
    }
}
