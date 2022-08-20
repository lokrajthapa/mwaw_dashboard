<?php

namespace App\Console\Commands;

use App\Mail\jobcategoryemail;
use Illuminate\Console\Command;
use App\Models\Job;
use App\Models\Category;
use App\Models\User;
use App\Notifications\SendJobNumberByCategoryNotification;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class jobsCategoryEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:jobsCategoryEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'it notify about the job registration daily ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {  
        //category->tile & jobscout

     
        $categories = Category::query()->withCount(['jobs'=>function($q)
        {
            $q->whereDate('created_at', \Carbon\Carbon::today(new DateTimeZone(timezone:'America/Toronto')));
        }])->get();


       // $todaydate = \Carbon\Carbon::now();
       
        //$jobsalertmails = Job::whereDate('created_at', $todaydate)->get(); 

//$data =[];

        foreach($categories as $category)
        {
             $this->info($category->name);
             $this->info($category->jobs_count);
        }
        

        
    
    $users = User::query()->where(column:'role',operator:'admin')->get();
    foreach($users as $user)
    {
        Notification::send($user, new SendJobNumberByCategoryNotification($categories));

    }

    // private function SendEmailToUser($id, $jobsalertmails)
    // { 
       
    //      //$jobsalertemails=Job::find($id);
    //      Mail::to('lokraj@gmail.com')->send(new jobcategoryemail($jobsalertmails));
    // }
    }



}
