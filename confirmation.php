<?php
// start the session
session_start();

// check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  // if not, redirect to the login page
  header("Location: login.php");
  exit();
}

// display the confirmation message
echo "<h1>Thank you for your order!</h1>";
echo "<p>Your order has been received and is being processed.</p>";
echo "<p>Order date: " . date('Y-m-d') . "</p>";

// retrieve the order details from the database
$user_id = $_SESSION['user_id'];
include('includes/connection.php');

$qorder = "SELECT * FROM order_tbl WHERE userid = ?";
$stmt = $conn->prepare($qorder);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// display the order details
echo "<h2>Order Details</h2>";
echo "<p><strong>First Name:</strong> " . $order['firstname'] . "</p>";
echo "<p><strong>Last Name:</strong> " . $order['lastname'] . "</p>";
echo "<p><strong>Email:</strong> " . $order['email'] . "</p>";
echo "<p><strong>Address 1:</strong> " . $order['address1'] . "</p>";
echo "<p><strong>Address 2:</strong> " . $order['address2'] . "</p>";
echo "<p><strong>ZIP Code:</strong> " . $order['zip'] . "</p>";
echo "<p><strong>Order Date:</strong> " . $order['orderdate'] . "</p>";
echo "<p><strong>Billing Information:</strong> " . $order['billinginfo'] . "</p>";
echo "<p><strong>Card Name:</strong> " . $order['cardname'] . "</p>";
echo "<p><strong>Card Number:</strong> **** **** **** " . substr($order['cardnum'], -4) . "</p>";
echo "<p><strong>Total Quantity:</strong> " . $order['quantity'] . "</p>";
echo "<p><strong>Total Price:</strong> $" . $order['totalprice'] . "</p>";
?>
