<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReport;

class Mycommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mycommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startOfDay = Carbon::today()->startOfDay();
    $endOfDay = Carbon::today()->endOfDay();


    $orderCount = Order::where('created_at', '>=', $startOfDay)
                       ->where('created_at', '<=', $endOfDay)
                       ->count();

                       $reportContent = "Total Orders Placed Today: {$orderCount}";
                       $this->info($reportContent);
                       $users = User::all();
                       foreach ($users as $user) {
                           Mail::to($user->email)->send(new DailyReport($orderCount));
                       }

    }
}
