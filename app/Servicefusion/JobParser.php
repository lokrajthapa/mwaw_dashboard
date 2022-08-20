<?php

namespace App\Servicefusion;

use App\Models\Customer;
use App\Models\Status;
use App\Models\SubCategory;
use App\Models\User;
use Carbon\Carbon;

class JobParser
{
    static public function parse($job): array
    {
        $customer = Customer::query()->where('sf_id', $job['customer_id'])->first();
        $customerId = $customer?->id;
        $status = Status::query()->where('name', $job['status'])->first();
        $statusId = $status?->id;
        $subCategory = SubCategory::query()->where('name', $job['category'])->first();
        $subCategoryId = $subCategory?->id;
        $categoryId = $subCategory?->category_id;

        $assignedTechs = [];
        foreach ($job['techs_assigned'] as $tech) {
            $tech = User::query()->where('sf_id', $tech['id'])->first();
            if ($tech) {
                $assignedTechs[] = $tech->id;
            }
        }
        return [
            'sf_id' => $job['id'],
            'title' => $job['number'],
            'description' => $job['description'],
            'sub_category_id' => $subCategoryId,
            'category_id' => $categoryId,
            'status_id' => $statusId,
            'customer_id' => $customerId,
            'sf_job_number' => $job['number'],
            'assignedTechs' => $assignedTechs,
            'created_at' => new Carbon($job['created_at']),
            'updated_at' => new Carbon($job['updated_at']),
            'start_date' => $job['start_date'],
            'end_date' => $job['end_date'],
            'start_time' => $job['time_frame_promised_start'],
            'end_time' => $job['time_frame_promised_end'],
            'po' => $job['po_number'],
            'customFields' => $job['custom_fields'],
            'services' => $job['services'],
            'payments' => $job['payments'],
        ];
    }
}
