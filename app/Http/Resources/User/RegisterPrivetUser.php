<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterPrivetUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $token = $this->createToken('')->plainTextToken;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'access_token' => $token,
            'full_token' => 'Bearer ' . $token,
            'avatar' => url('') . '/storage/images/avatar/' . $this->avatar,
        ];
    }
}
