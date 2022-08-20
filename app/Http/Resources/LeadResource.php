<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'gclid' => $this->gclid,
            'source' => $this->source,
            'email' => $this->email,
            'phone_no' => $this->phone_no,
            'conversion_datetime' => $this->conversion_datetime,
            'conversion_value' => $this->conversion_value,
            'conversion_currency' => $this->conversion_currency,
            'job_id' => $this->job_id,
            'uploaded' => $this->uploaded,
            'job' => new JobResource($this->whenLoaded('job'))
        ];
    }
}
