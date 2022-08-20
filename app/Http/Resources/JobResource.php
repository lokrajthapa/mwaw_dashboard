<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'description' => $this->description,
            'category_id' => $this->category_id,
            'status_id' => $this->status_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'status' => new StatusResource($this->whenLoaded('status')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subCategory' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'technicians' => UserResource::collection($this->whenLoaded('technicians')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'sf_id' => $this->sf_id,
            'po' => $this->po
        ];
    }
}
