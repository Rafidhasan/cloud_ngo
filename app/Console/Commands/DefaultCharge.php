<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

use App\BusinessLoan;
use App\EduLoan;
use App\EmployeeLoan;

use App\SavingAcount;

use App\LoanInstallment;

class DefaultCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:everyMonth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Status of ddefault charge';

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

        foreach ($users as $user) {
            $user_id = $user->id;

            if($user->hasBusinessLoans() == $user_id) {
                $loan = BusinessLoan::where('user_id', $user->id)->where('completed', 0)->first();
                $loan_installments = LoanInstallment::where('loan_id', $loan->id)->latest()->first();

                if($loan_installments == '') {
                    // user doesn't give first installment
                    $saving = SavingAcount::where('user_id', $user->id)->latest()->first();
                    $saving->total = $saving->total - 20;
                    $saving->save();

                    $username = "Alauddin101";
                    $hash = "4f9ec55ab0531a44a466910119d97847";
                    $numbers = $user->mobile_number; //Recipient Phone Number multiple number must be separated by comma
                    $message = '20tk has been deducted for your late of Loan from saving. Thank you! Your current saving is '.$saving->total;

                    $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                    $response = curl_exec($ch);
                    curl_close ($ch);
                }   else {
                    $to = \Carbon\Carbon::parse($loan->approved_date)->floorMonth();
                    $from = \Carbon\Carbon::parse($loan_installments->this_month)->floorMonth();
                    $net_installment_month = $to->diffInMonths($from);

                    if($loan_installments->amount < $net_installment_month * $loan->perInstallmentAmount) {
                        $saving = SavingAcount::where('user_id', $user->id)->latest()->first();
                        $saving->total = $saving->total - 20;
                        $saving->save();

                        $username = "Alauddin101";
                        $hash = "4f9ec55ab0531a44a466910119d97847";
                        $numbers = $user->mobile_number; //Recipient Phone Number multiple number must be separated by comma
                        $message = '20tk has been deducted for less amount then per installment amount of Loan from saving. Thank you! Your current saving is '.$saving->total;

                        $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                        $response = curl_exec($ch);
                        curl_close ($ch);

                        return 0;
                    }   else {
                        return 0;
                    }
                }
            }   elseif($user->hasEmployeeLoans() == $user_id) {

            }   elseif($user->hasEduLoans() == $user_id) {

            }   else {
                // user has no loan
                return 0;
            }
        }
    }
}
