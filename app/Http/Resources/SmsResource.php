<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsResource extends JsonResource
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
            'job_id' => $this->job_id,
            'from' => $this->from,
            'to' => $this->to,
            'email_sent' => $this->email_sent,
            'body' => $this->body,
            'type' => $this->type,
            'created_at' => $this->created_at
        ];
    }
}
