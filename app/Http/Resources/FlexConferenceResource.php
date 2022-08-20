<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlexConferenceResource extends JsonResource
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
            'direction' => $this->direction,
            'created_at' => $this->created_at,
            'recording' => new FlexRecordingResource($this->whenLoaded('recording')),
            'calls' => FlexCallResource::collection($this->whenLoaded('calls'))
        ];
    }
}
