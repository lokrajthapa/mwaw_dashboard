<?php

namespace App\Console\Commands;

use App\Geocoder\Geocoder;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCustomersLatLng extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-customers-latlng';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer address coordinates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = Customer::all();
        $geocoder = new Geocoder();
        foreach ($customers as $customer) {
            if ((!$customer->latitude) && (!$customer->longitude)) {
                if ($customer->street_address && $customer->locality) {
                    $address = implode(',', [
                        $customer->street_address,
                        $customer->locality,
                        $customer->province,
                        $customer->postal_code,
                        $customer->country,
                    ]);
                    try {
                        $location = $geocoder->getCoordinates($address);
                        $customer->update($location);
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            }
        }
        return 0;
    }
}
