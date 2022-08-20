<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleHistory;
use Illuminate\Console\Command;

class UpdateVehicleStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-vehicle-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates vehicle status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $vehicles = Vehicle::query()->get();
        $this->info($vehicles->count());
        foreach ($vehicles as $vehicle) {
            try {
                $vehicle->load('latestStatus');
                $status = $vehicle->getCurrentStatus();
                if (!$vehicle->latestStatus->isEmpty()) {
                    $latestStatus = $vehicle->latestStatus->first();
                    $this->info($vehicle->vin . $latestStatus->status['gps']['timestamp'] . $status['gps']['timestamp']);
                    if ($latestStatus->status['gps']['timestamp'] == $status['gps']['timestamp']) {
                        $latestStatus->status = $status;
                        $latestStatus->save();
                    } else {
                        VehicleHistory::create([
                            'vehicle_id' => $vehicle->id,
                            'status' => $status
                        ]);
                    }
                } else {
                    VehicleHistory::create([
                        'vehicle_id' => $vehicle->id,
                        'status' => $status
                    ]);
                }

            } catch (\Exception $e) {
                error_log($vehicle->vin . ': ' . $e->getMessage());
            }
        }
        return 0;
    }
}
