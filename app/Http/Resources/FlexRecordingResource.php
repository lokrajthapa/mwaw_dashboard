<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlexRecordingResource extends JsonResource
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
            'conference_sid' => $this->conference_sid,
            'call_sid' => $this->call_sid,
            'account_sid' => $this->account_sid,
            'duration' => $this->duration,
            'source' => $this->source,
            'media_url' => $this->media_url,
            'created_at' => $this->created_at,
        ];
    }
}
