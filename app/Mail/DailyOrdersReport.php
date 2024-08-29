<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyOrdersReport extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $orderCount;
    public $totalSales;

    public function __construct($filePath, $orderCount, $totalSales)
    {
        $this->filePath = $filePath;
        $this->orderCount = $orderCount;
        $this->totalSales = $totalSales;
    }

    public function build()
    {
        return $this->to(config('mail.from.address'))
            ->subject('Daily Orders Report - ' . now()->format('Y-m-d'))
            ->view('emails.daily_orders_report')
            ->attach($this->filePath)
            ->with([
                'orderCount' => $this->orderCount,
                'totalSales' => $this->totalSales,
            ]);
    }
}
