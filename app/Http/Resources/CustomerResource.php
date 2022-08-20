<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_no' => $this->phone_no,
            'street_address' => $this->street_address,
            'locality' => $this->locality,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'buzzer' => $this->buzzer,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'authorized' => $this->authorized,
            'owner_first_name' => $this->owner_first_name,
            'owner_last_name' => $this->owner_last_name,
            'owner_phone_no' => $this->owner_phone_no,
            'owner_email' => $this->owner_email,
            'created_at' => $this->created_at,
            'jobs' => JobResource::collection($this->whenLoaded('jobs'))
        ];
    }
}
