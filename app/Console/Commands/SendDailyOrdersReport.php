<?php

namespace App\Console\Commands;

use App\Exports\OrdersExport;
use App\Mail\DailyOrdersReport;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendDailyOrdersReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily-orders-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily email with a spreadsheet of the day\'s orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::with(['user', 'orderItems.product',  'paymentMethod', 'address'])
            ->whereBetween('created_at',  [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()])
            ->get();

        $filePath = storage_path('app/daily_orders_report_' . Carbon::yesterday()->format('Y-m-d') . '.xlsx');
        Excel::store(new OrdersExport($orders), 'daily_orders_report_' . Carbon::yesterday()->format('Y-m-d') . '.xlsx');

        // Send the email with the spreadsheet attached
        Mail::to(config('admin.email'))->send(new DailyOrdersReport($filePath, $orders->count(), $orders->sum('total')));

        $this->info('Daily orders report email sent successfully!');
    }

}
