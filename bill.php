<?php
// bill.php
//require_once 'config.php';

// Create a PDO connection instance
//$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function generateBill(int $orderId, PDO $conn): string
{
    // Get order details
    $getOrderDetailsQuery = "SELECT * FROM orders WHERE id = :order_id";
    $orderResult = $conn->prepare($getOrderDetailsQuery);
    $orderResult->bindParam(':order_id', $orderId);
    $orderResult->execute();
    $orderData = $orderResult->fetch();

    // Get order items
    $getOrderItemsQuery = "SELECT * FROM total_products WHERE order_id = :order_id";
    $orderItemsResult = $conn->prepare($getOrderItemsQuery);
    $orderItemsResult->bindParam(':order_id', $orderId);
    $orderItemsResult->execute();

    // Generate bill
    $bill = "
        <h1>Bill for Order #$orderId</h1>
        <p>Customer Name: {$orderData['customer_name']}</p>
        <p>Order Date: {$orderData['order_date']}</p>
        <table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
    ";

    while ($orderItem = $orderItemsResult->fetch()) {
        $bill .= "
            <tr>
                <td>{$orderItem['product_name']}</td>
                <td>{$orderItem['quantity']}</td>
                <td>{$orderItem['price']}</td>
                <td>" . ($orderItem['quantity'] * $orderItem['price']) . "</td>
            </tr>
        ";
    }

    $bill .= "
        </table>
        <p>Total: {$orderData['total']}</p>
    ";

    return $bill;
}

// Example usage
$orderId = 1;
//$bill = generateBill($orderId, $conn);
//echo $bill;
