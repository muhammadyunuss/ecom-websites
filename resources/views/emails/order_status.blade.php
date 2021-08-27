<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table style="width: 700px;">
        <tr><td>&nbsp;</td></tr>
        <tr><td><img src="{{ asset('images/front_images/ecom-logo.png') }}" alt=""></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>Hello {{ $name }}</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>You order #{{ $order_id }} status has been updated to {{ $order_status }}.</td></tr>
        <tr>
            @if(!empty($courier_name) && !empty($tracking_number))
            <tr><td>&nbsp;</td></tr>
            <tr><td>Courier Name is {{ $courier_name }} and Tracking Number is {{ $tracking_number }}</td></tr>
            @endif
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>Your order details are as below :-</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>
            <table style="width: 95%;" cellpadding="5" cellspacing="5" bgcolor="#f7f4f4">
                <tr bgcolor="#cccccc">
                    <td>Name</td>
                    <td>Code</td>
                    <td>Size</td>
                    <td>Color</td>
                    <td>Quantity</td>
                    <td>Price</td>
                </tr>
                @foreach ($orderDetails['orders_products'] as $order)
                    <tr bgcolor="cccccc">
                        <td>{{ $order['product_name'] }}</td>
                        <td>{{ $order['product_code'] }}</td>
                        <td>{{ $order['product_size'] }}</td>
                        <td>{{ $order['product_color'] }}</td>
                        <td>{{ $order['product_qty'] }}</td>
                        <td>Rp. {{ $order['product_price'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" align="right">Shipping Charges</td>
                    <td>Rp. {{ $orderDetails['shipping_charges'] }}</td>
                </tr>
                <tr>
                    <td colspan="5" align="right">Coupon Diskon</td>
                    <td>Rp.
                        @if ($orderDetails['coupon_amount']>0)
                            {{ $orderDetails['coupon_amount'] }}</td>
                        @else
                            0
                        @endif
                </tr>
                <tr>
                    <td colspan="5" align="right">Grand Total</td>
                    <td>Rp. {{ $orderDetails['grand_total'] }}</td>
                </tr>
            </table>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>
            <table>
                <tr>
                    <td><strong>Delivery Address</strong></td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['name'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['address'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['city'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['state'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['country'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['pincode'] }}</td>
                </tr>
                <tr>
                    <td>{{ $orderDetails['mobile'] }}</td>
                </tr>
            </table>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>For any enquiries, you can contact us at</td><a href="mailto:kknuntagmenurpumpungan@gmail.com">send info to us</a></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>Regards,<br>Team Stack KKN</td></tr>
        <tr><td>&nbsp;</td></tr>
    </table>
</body>
</html>
