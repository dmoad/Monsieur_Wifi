<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Mail\OrderProcessedMail;
use Illuminate\Support\Facades\Mail;

$order = Order::with(['user', 'items.productModel', 'shippingAddress', 'billingAddress'])
    ->where('order_number', 'ORD-202602-00013')
    ->first();

if (!$order) {
    die("Order not found");
}

echo "<h1>Order Email Test</h1>";
echo "<p><strong>Order ID:</strong> {$order->id}</p>";
echo "<p><strong>User ID:</strong> {$order->user_id}</p>";
echo "<p><strong>User Name:</strong> " . ($order->user ? $order->user->name : 'NULL') . "</p>";
echo "<p><strong>User Email:</strong> " . ($order->user ? $order->user->email : 'NULL') . "</p>";
echo "<hr>";

echo "<h2>Creating Mail Object...</h2>";
$mail = new OrderProcessedMail($order, 'en');

echo "<p><strong>Mail order user:</strong> " . ($mail->order->user ? $mail->order->user->name : 'NULL') . "</p>";
echo "<hr>";

echo "<h2>Rendering Email View...</h2>";
try {
    $view = view('emails.order-processed-en', ['order' => $mail->order])->render();
    echo "<p style='color:green;'><strong>✅ Email rendered successfully!</strong></p>";
    echo "<p>Email size: " . strlen($view) . " bytes</p>";
    echo "<details><summary>View HTML</summary><pre>" . htmlentities(substr($view, 0, 500)) . "...</pre></details>";
} catch (\Exception $e) {
    echo "<p style='color:red;'><strong>❌ Failed to render email:</strong></p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>⚠️ Delete this file:</strong> rm /var/www/mrwifi/public/test-email-direct.php</p>";
