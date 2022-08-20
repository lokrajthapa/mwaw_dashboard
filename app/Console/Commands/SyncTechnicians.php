<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Servicefusion\Servicefusion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncTechnicians extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-technicians';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync servicefusion technicians';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $servicefusion = new Servicefusion();
        $technicians = $servicefusion->getAllTechnicians();
        foreach ($technicians as $technician) {
            $parsedTechnician = $this->parseTechnician($technician);
            try {
                $user = User::query()->where('role', 'technician')->where('sf_id', $parsedTechnician['sf_id'])->first();
                if ($user) {
                    $user->update($parsedTechnician);
                } else {
                    $parsedTechnician['password'] = Hash::make('password');
                    User::create($parsedTechnician);
                }
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        return 0;
    }

    private function parseTechnician(mixed $technician): array
    {
        return [
            'name' => $technician['first_name'] . ' ' . $technician['last_name'],
            'email' => $technician['email'],
            'role' => 'technician',
            'phone_no' => $technician['phone_1'],
            'sf_id' => $technician['id'],
            'email_verified_at' => Carbon::now(),
        ];
    }
}
