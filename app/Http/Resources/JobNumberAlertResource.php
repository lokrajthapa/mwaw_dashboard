<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobNumberAlertResource extends JsonResource
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
            'no_of_jobs' => $this->no_of_jobs,
            'days' => $this->days,
            'condition' => $this->condition,
            'category_id' => $this->category_id,
            'receivers' => $this->receivers,
            'last_alert' => $this->last_alert,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at
        ];
    }
}
