<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{

    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'User ID',
            'Status',
            'Total',
            'Items',
            'Address',
            'Payment Method',
            'Created At',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        $items = $row->orderItems->map(function ($item) {
            return [
                'Product ID' => $item->product_id,
                'Name' => $item->product->name,
                'Quantity' => $item->quantity,
                'Price' => $item->product->price,
                'Total Price' => $item->quantity * $item->product->price,
            ];
        });

        $address = [
            "Recipient's Name" => $row->address->name,
            'Address Line 1' => $row->address->address_line1,
            'Address Line 2' => $row->address->address_line2,
            'City' => $row->address->city,
            'State' => $row->address->state,
            'Postal Code' => $row->address->postal_code,
            'Country' => $row->address->country,
            'Phone Number' => $row->address->phone_number,
        ];

        return [
            $row->id,
            $row->user_id,
            $row->status,
            $row->total,
            json_encode($items),  // Convert items array to JSON string
            json_encode($address),  // Convert address array to JSON string
            $row->paymentMethod->type,
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
