<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Servicefusion\Servicefusion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-customers {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync service fusion customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $servicefusion = new Servicefusion();
        if ($this->option('all')) {
            $customers = $servicefusion->getAllCustomers();
        } else {
            $customers = $servicefusion->getLatestCustomers(app()->environment('production') ? 20 : 10);
        }
        foreach ($customers as $customer) {
            $parsedCustomer = $this->parseCustomer($customer);
            try {
                Customer::query()
                    ->updateOrCreate(['sf_id' => $parsedCustomer['sf_id']], $parsedCustomer);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        return 0;
    }

    public function parseCustomer(mixed $customer): array
    {
        $data = [
            'sf_id' => $customer['id']
        ];
        if (key_exists('contacts', $customer)) {
            $contact = $customer['contacts'][0];
            $data['first_name'] = $contact['fname'];
            $data['last_name'] = $contact['lname'];

            if (key_exists('phones', $contact)) {
                $phone = $contact['phones'][0];
                $data['phone_no'] = $phone['phone'];
            }

            if (key_exists('emails', $contact)) {
                $email = $contact['emails'][0];
                $data['email'] = $email['email'];
            }
        }

        if (key_exists('locations', $customer)) {
            $location = $customer['locations'][0];
            $data['street_address'] = $location['street_1'];
            $data['locality'] = $location['city'];
            $data['province'] = $location['state_prov'];
            $data['postal_code'] = $location['postal_code'];
            $data['country'] = $location['country'];
            $data['latitude'] = $location['latitude'] ?: null;
            $data['longitude'] = $location['longitude'] ?: null;
        }
        return $data;
    }
}
