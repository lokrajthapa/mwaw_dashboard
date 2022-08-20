<?php

namespace App\Console\Commands;

use App\Fordpass\Fordpass;
use Illuminate\Console\Command;

class TestFuntions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test-functions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Playground to test functions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fordpass = new Fordpass();
        try{
            $token = $fordpass->getAccessTokenFromCredentials();

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        dd($token);
        return 0;
    }
}
