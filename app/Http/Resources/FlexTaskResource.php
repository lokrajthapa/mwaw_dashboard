<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlexTaskResource extends JsonResource
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
            'sid' => $this->sid,
            'age' => $this->age,
            'status' => $this->status,
            'attributes' => $this->attributes,
            'queue_name' => $this->queue_name,
            'channel_name' => $this->channel_name,
            'workers' => UserResource::collection($this->whenLoaded('workers')),
            'created_at' => $this->created_at
        ];
    }
}
