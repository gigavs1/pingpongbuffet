<?php
include('includes/connection.php');

// Get the POST data
$productName = $_POST['product_name'];
$action = $_POST['action'];

$user_id = $_SESSION['user_id'];

// Update the database
if ($action === 'subtract') {
  $q = "UPDATE cart_tbl SET productquantity = productquantity - 1 WHERE userid = ? AND productname = ?";
  $statement = $conn->prepare($q);
  $statement->bind_param("ss", $user_id, $productName);
  $statement->execute();

  // Get the updated product data
  $q = "SELECT productprice, productquantity FROM cart_tbl WHERE userid = ? AND productname = ?";
  $statement = $conn->prepare($q);
  $statement->bind_param("ss", $user_id, $productName);
  $statement->execute();
  $result = $statement->get_result();
  $row = $result->fetch_assoc();
  $price = $row['productprice'];
  $quantity = $row['productquantity'];
  $subtotal = (int)$price * (int)$quantity;
  $total = $subtotal;

  // Return the updated product data
  echo json_encode([
    'success' => true,
    'price' => number_format($price, 2, '.', ','),
'quantity' => $quantity,
'subtotal' => number_format($subtotal, 2, '.', ','),
'total' => number_format($total, 2, '.', ',')
]);

} elseif ($action === 'delete') {
$q = "DELETE FROM cart_tbl WHERE userid = ? AND productname = ?";
$statement = $conn->prepare($q);
$statement->bind_param("ss", $user_id, $productName);
$statement->execute();

// Return success response
echo json_encode([
'success' => true
]);
}

// Close database connection
$statement->close();
$conn->close();

?>