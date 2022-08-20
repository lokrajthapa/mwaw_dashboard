<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RegisterAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:register-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers admin user account';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('Enter name:');
        $email = $this->ask('Enter email:');
        $password = Hash::make($this->secret('Enter password:'));
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => Carbon::now(),
            'role' => 'admin'
        ]);
        $this->info("A user with user id {$user->id} has been created successfully.");
        return 0;
    }
}
