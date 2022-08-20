<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'category_id' => $this->category_id,
            'display_role' => $this->display_role,
            'email_verified_at' => $this->email_verified_at,
            'phone_no' => $this->phone_no,
            'street_address' => $this->street_address,
            'locality' => $this->locality,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'agent_id' => $this->agent_id
        ];
    }
}
