<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\SavingAcount;

use App\UserNotification;

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

                // Service charge goes to accounts
                $accounts = new Accounts();
                $accounts->service_charge = 20;
                $accounts->user_id = $user->id;

                $row = count(Accounts::select('service_charge')->where('user_id', $user->id)->get());
                if($row == 0) {
                    $accounts->total_service_charge = 20;
                    $accounts->total = 20;
                }   else {
                    $prev_fees = Accounts::where('user_id', $user->id)->latest()->first();
                    $accounts->total_service_charge = 20 + $prev_amount->total_service_charge;
                    $accounts->total = $accounts->total_service_charge + $accounts->total_default_charge + $accounts->total_fee;
                }

                $accounts->save();

                $username = "Alauddin101";
                $hash = "4f9ec55ab0531a44a466910119d97847";
                $numbers = $user->mobile_number; //Recipient Phone Number multiple number must be separated by comma
                $message = '20tk has been deducted for your service charge. Thank you! Your current saving is '.$saving->total;

                $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                $response = curl_exec($ch);
                curl_close ($ch);

                $accounts->save();
            }
        }
    }
}
