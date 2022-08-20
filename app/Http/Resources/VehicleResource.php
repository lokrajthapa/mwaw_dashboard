<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'title' => $this->title,
            'vin' => $this->vin,
            'user_id' => $this->user_id,
            'technician' => new UserResource($this->whenLoaded('technician')),
            'locationHistory' => VehicleHistoryResource::collection($this->whenLoaded('locationHistory')),
            'latestStatus' => VehicleHistoryResource::collection($this->whenLoaded('latestStatus'))
        ];
    }
}
