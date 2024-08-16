@component('mail::message')
    # Order Confirmation

    Thank you for your purchase, {{ $order->user->name }}!

    Your order has been successfully placed. Below are the details of your order:


    **Total:** ${{ number_format($order->total, 2) }}

    We will notify you once your order is shipped.

    Thanks for shopping with us!

@endcomponent
