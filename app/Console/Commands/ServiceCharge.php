<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\SavingAcount;

use App\User;

use DB;

use App\Accounts;

use App\ServiceChrg;

class ServiceCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:juneFirst';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Your Service Charge is automatically cut from your savings by our system. Thanks for stay with us';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::get();

        foreach($users as $user) {
            $total = 0;
            if($user->hasSavings() == $user->id) {
                $service_charges = new ServiceChrg();
                $saving = SavingAcount::where('user_id', $user->id)
                    ->latest()
                    ->first();
                $saving->total = $saving->total - 20;
                $saving->save();

                $service_charges->user_id = $user->id;
                $service_charges->save();

                $total += 20;

                $accounts = new Accounts();
                $user_id = $user->id;
                $accounts->user_id = $user_id;
                $accounts->total_service_charge = $total;

                $accounts->save();
            }

        }
    }
}
