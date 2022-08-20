<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlexCallResource extends JsonResource
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
            'status' => $this->status,
            'direction' => $this->direction,
            'from' => $this->from,
            'to' => $this->to,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
            'recording' => new FlexRecordingResource($this->whenLoaded('recording'))
        ];
    }
}
