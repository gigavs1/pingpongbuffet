<?php
    session_start();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="assets/img/favicon.png" rel="icon">

    <!-- font-awesome script -->
    <script src="https://kit.fontawesome.com/de92694c6a.js" crossorigin="anonymous"></script>

    <title>Checkout</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/checkout/">

    <!-- Bootstrap core CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="form-validation.css" rel="stylesheet">
  </head>

  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <h2>Checkout form</h2>
      </div>

      <div class="row">

      <div class="col-md-4 order-md-2 mb-4">
  <h4 class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted">Your cart</span>
    <?php
      include('includes/connection.php');
      $user_id = $_SESSION['user_id'];
      $q = "SELECT COUNT(*) FROM cart_tbl WHERE userid = ?";
      $count_statement = $conn->prepare($q);
      $count_statement->bind_param("s", $user_id);
      $count_statement->execute();
      $count_statement->bind_result($product_count);
      $count_statement->fetch();
      $count_statement->close();
      echo "<span class='badge badge-secondary badge-pill'>$product_count</span>";
    ?>
  </h4>
  <ul class="list-group mb-3">
    <?php
    if(isset($_POST['subtract_qty']) && isset($_POST['product_name'])) {
      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $q = "UPDATE cart_tbl SET productquantity = productquantity - 1 WHERE userid = ? AND productname = ? AND productprice = ? AND productquantity > 0 AND cartid = (SELECT MIN(cartid) FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ?)";
      $statement = $conn->prepare($q);
      $statement->bind_param("ssssss", $user_id, $product_name, $product_price, $user_id, $product_name, $product_price);
      $statement->execute();
  
      // Check if the updated quantity is zero and delete the row if so
      $affected_rows = $statement->affected_rows;
      if($affected_rows > 0) {
          $check_qty = "SELECT productquantity FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ?";
          $check_stmt = $conn->prepare($check_qty);
          $check_stmt->bind_param("sss", $user_id, $product_name, $product_price);
          $check_stmt->execute();
          $check_stmt->bind_result($quantity);
          $check_stmt->fetch();
          $check_stmt->close();
  
          if($quantity == 0) {
              $delete_q = "DELETE FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ?";
              $delete_stmt = $conn->prepare($delete_q);
              $delete_stmt->bind_param("sss", $user_id, $product_name, $product_price);
              $delete_stmt->execute();
              $delete_stmt->close();
          }
      }
  
      $statement->close();
  } elseif(isset($_POST['delete_product']) && isset($_POST['product_name'])) {
      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $q = "DELETE FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ? AND cartid = (SELECT MIN(cartid) FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ?)";
      $statement = $conn->prepare($q);
      $statement->bind_param("ssssss", $user_id, $product_name, $product_price, $user_id, $product_name, $product_price);
      $statement->execute();
      $statement->close();
  }
      
      $q = "SELECT productname, productprice, SUM(productquantity) AS total_quantity
            FROM cart_tbl
            WHERE userid = ?
            GROUP BY productname";
      $statement = $conn->prepare($q);
      $statement->bind_param("s", $user_id);
      $statement->execute();
      $result = $statement->get_result();
      $total = 0;
      while ($row = $result->fetch_assoc()) {
        $name = $row['productname'];
        $price = $row['productprice'];
        $quantity = $row['total_quantity'];
        $subtotal = (int)$price * (int)$quantity;
        $total += $subtotal;
        $productPrice = 'P '.number_format($price, 2, '.', ','); 
        echo "<li class='list-group-item d-flex justify-content-between lh-condensed'>
                <div>
                  <h6 class='my-0'>$name</h6>
                  <small class='text-muted'>$productPrice x $quantity</small>
                </div>
                <form method='post' action=''>
                  <input type='hidden' name='product_name' value='$name'>
                  <input type='hidden' name='product_price' value='$price'>
                  <div class='d-flex align-items-center justify-content-center'>
                    <button type='submit' name='subtract_qty' class='btn btn-link'>
                      <i class='fa-solid fa-square-minus' style='color: #fa0000; margin-right: 10px;'></i>
                    </button>
                    <button type='submit'name='delete_product' class='btn btn-link'>
                    <i class='fa-solid fa-trash' style='color: #fa0000;'></i>
                    </button>
                    </div>
                    </form>
                    </li>";
                    }
        $statement->close();
        $conn->close();
        ?>
        <li class="list-group-item d-flex justify-content-between">
        <span>Total (PHP)</span>
        <strong>P <?php echo number_format($total, 2, '.', ','); ?></strong>
        </li>
        
          </ul>
        </div>
        
        <?php 

          use PHPMailer\PHPMailer\PHPMailer;
          use PHPMailer\PHPMailer\SMTP;
          use PHPMailer\PHPMailer\Exception;

          require 'PHPMailer/src/Exception.php';
          require 'PHPMailer/src/PHPMailer.php';
          require 'PHPMailer/src/SMTP.php';

            if (isset($_POST['checkout'])) {

              include('includes/connection.php');
              
              $user_id = $_SESSION['user_id'];

              // encrypt the password for security
              $encrypt = password_hash($_POST['cc-number'], PASSWORD_DEFAULT);

              include('includes/connection.php');

              $datetoday = date('Y-m-d');

              $qcart = "SELECT product_tbl.productprice, cart_tbl.productquantity
                        FROM cart_tbl
                        INNER JOIN product_tbl ON cart_tbl.productname = product_tbl.productid
                        WHERE cart_tbl.userid = ?";
              $stmt = $conn->prepare($qcart);
              $stmt->bind_param("i", $user_id);
              $stmt->execute();

              $productquantity = array();
              $productprice = array();
              $total_price = 0;

              $stmt->bind_result($productprice, $productquantity);
              while ($stmt->fetch()) {
                  $productquantity[] = $productquantity;
                  $productprice[] = $productprice;
              }

              $stmt->close();

              // Calculate the total price
              for ($i = 0; $i < count($productquantity); $i++) {
                  $total_price += $productquantity[$i] * $productprice[$i];
              }

              // Get the cart ID for the user
              $get_cart_id_query = "SELECT cartid FROM cart_tbl WHERE userid = ?";
              $cart_id_statement = $conn->prepare($get_cart_id_query);
              $cart_id_statement->bind_param("i", $user_id);
              $cart_id_statement->execute();
              $cart_id_result = $cart_id_statement->get_result();
              $cart_id_row = $cart_id_result->fetch_assoc();
              $cart_id = $cart_id_row['cartid'];

              // Insert the order into the order_tbl table
              $qcheckout = "INSERT INTO order_tbl 
                            (cartid, userid, firstname, lastname, email, address1, address2, zip, orderdate, billinginfo, cardname, cardnum, quantity, totalprice) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

              $statement = $conn->prepare($qcheckout);
              $statement->bind_param("iisssssssssdds", 
                  $cart_id, 
                  $user_id,
                  $_POST['firstname'],
                  $_POST['lastname'],
                  $_POST['email'],
                  $_POST['address1'],
                  $_POST['address2'],
                  $_POST['zip'],
                  $datetoday,
                  $_POST['paymentMethod'],
                  $_POST['cc-name'],
                  $encrypt,
                  $quantities,
                  $total_price
              );
              $statement->execute();


              $statement->execute();

              $statement->close();

              $qclear = "DELETE FROM cart_tbl WHERE userid = ?";
              $stmt = $conn->prepare($qclear);
              $stmt->bind_param("i", $user_id);
              $stmt->execute();
              $stmt->close();

              unset($_POST['checkout']);
              unset($_SESSION['token']);

              // Send email to user
              $to = $_POST['email'];
              $subject = "Your order has been received";
              $message = "Thank you for your order! Here are the details:\n\n";
              $message .= "Order date: " . $datetoday . "\n";
              $message .= "Total price: $" . $total_price . "\n\n";

              // Add the user's name and address
              $message .= "Shipping Address:\n";
              $message .= $_POST['firstname'] . " " . $_POST['lastname'] . "\n";
              $message .= $_POST['address1'] . "\n";
              if (!empty($_POST['address2'])) {
                  $message .= $_POST['address2'] . "\n";
              }
              $message .= $_POST['zip'] . "\n";

              // Add the list of products purchased
              $message .= "\nProducts Purchased:\n";
              for ($i = 0; $i < count($productquantity); $i++) {
                  $message .= "- " . $productquantity[$i] . " x " . $productprice[$i] . "\n";
              }

              // Configure PHPMailer
              $mail = new PHPMailer(true);

              try {
                  //Server settings
                  $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
                  $mail->isSMTP();                                            // Send using SMTP
                  $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
                  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                  $mail->Username   = 't3st12356789@gmail.com';                     // SMTP username
                  $mail->Password   = 'yasgjfpfvsljavki';                               // SMTP password
                  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                  $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                  //Recipients
                  $mail->setFrom('t3st12356789@gmail.com', 'admin');
                  $mail->addAddress($to);     // Add a recipient

                  //Content
                  $mail->isHTML(false);                                  // Set email format to plain text
                  $mail->Subject = $subject;
                  $mail->Body    = $message;

                  $mail->send();
                  echo 'Message has been sent';
              } catch (Exception $e) {
                  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
              }

              // Send email to admin
              $to = "t3st12356789@gmail.com";
              $subject = "New order received";
              $message = "A new order has been received! Here are the details:\n\n";
              $message .= "Order date: " . $datetoday . "\n";
              $message .= "Total price: $" . $total_price . "\n\n";

              // Add the user's name and address
              $message .= "Shipping Address:\n";
              $message .= $_POST['firstname'] . " " . $_POST['lastname'] . "\n";
              $message .= $_POST['address1'] . "\n";
              if (!empty($_POST['address2'])) {
                  $message .= $_POST['address2'] . "\n";
              }
              $message .= $_POST['zip'] . "\n";

              // Add the list of products purchased
              $message .= "\nProducts Purchased:\n";
              for ($i = 0; $i < count($productquantity); $i++) {
                $message .= "- " . $productquantity[$i] . " x " . $productprice[$i] . "\n";
              }

              // Configure PHPMailer for admin email
              $mail_admin = new PHPMailer(true);

              try {
              //Server settings
              $mail_admin->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
              $mail_admin->isSMTP(); // Send using SMTP
              $mail_admin->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
              $mail_admin->SMTPAuth = true; // Enable SMTP authentication
              $mail_admin->Username = 't3st12356789@gmail.com'; // SMTP username
              $mail_admin->Password = 'yasgjfpfvsljavki'; // SMTP password
              $mail_admin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; PHPMailer::ENCRYPTION_SMTPS encouraged
              $mail_admin->Port = 587; // TCP port to connect to, use 465 for PHPMailer::ENCRYPTION_SMTPS above

              //Recipients
              $mail_admin->setFrom('your_email@gmail.com', 'Your Name');
              $mail_admin->addAddress($to);     // Add a recipient

              //Content
              $mail_admin->isHTML(false);                                  // Set email format to plain text
              $mail_admin->Subject = $subject;
              $mail_admin->Body    = $message;

              $mail_admin->send();
              echo 'Message has been sent';

              } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail_admin->ErrorInfo}";
                }


              // Redirect to confirmation page
              header("Location: confirmation.php");
              exit();
            }
          
          ?>
        
          <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Billing address</h4>
          <form class="needs-validation" novalidate method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          
						<input type="hidden" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>" name="token">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" id="firstName" name="firstname" placeholder="" value="" required>
                <div class="invalid-feedback">
                  Valid first name is required.
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="lastname" placeholder="" value="" required>
                <div class="invalid-feedback">
                  Valid last name is required.
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com">
              <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
              </div>
            </div>
            <div class="row">
            
            <div class="col-md-6 mb-3">
                <label for="address">Address</label>
                    <input type="text" class="form-control" id="address1" name="address1" placeholder="1234 Main St" required>
                        <div class="invalid-feedback">
                                    Please enter your shipping address.
                        </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="zip">Zip</label>
                    <input type="text" class="form-control" id="zip" name="zip" placeholder="" required>
                        <div class="invalid-feedback">
                        Zip code required.
                        </div>
                </div>
            </div>
            

            <div class="mb-3">
                <label for="address2">Address 2 <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control" id="address2" name="address2" placeholder="Apartment or suite">
            </div>

            <hr class="mb-4">

            <h4 class="mb-3">Payment</h4>

            <div class="d-block my-3">
                <div class="custom-control custom-radio">
                    <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked value="credit" required>
                    <label class="custom-control-label" for="credit">Credit card</label>
                </div>
                <div class="custom-control custom-radio">
                    <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" value="debit" required>
                    <label class="custom-control-label" for="debit">Debit card</label>
                </div>
                <div class="custom-control custom-radio">
                    <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" value="paypal" required>
                    <label class="custom-control-label" for="paypal">Paypal</label>
                </div>
                <div class="custom-control custom-radio">
                    <input id="COD" name="paymentMethod" type="radio" class="custom-control-input" value="COD" required>
                    <label class="custom-control-label" for="paypal">Cash on Delivery</label>
                </div>
                </div>

                <div id="creditCardForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                    <label for="cc-name">Name on card</label>
                    <input type="text" class="form-control" id="cc-name" name="cc-name" placeholder="" required>
                    <small class="text-muted">Full name as displayed on card</small>
                    <div class="invalid-feedback">
                        Name on card is required
                    </div>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label for="cc-number">Credit card number</label>
                    <input type="text" class="form-control" id="cc-number" name="cc-number" placeholder="" required>
                    <div class="invalid-feedback">
                        Credit card number is required
                    </div>
                    </div>
                </div>
                </div>

                <hr class="mb-4">

                <button class="btn btn-primary btn-lg btn-block" type="submit" name="checkout">Continue to checkout</button>
                <button class="btn btn-danger btn-lg btn-block"><a href="index.php">Cancel</a></button>

                <script>
                const creditCardForm = document.getElementById("creditCardForm");
                const radioButtons = document.querySelectorAll('input[type="radio"][name="paymentMethod"]');
                
                radioButtons.forEach((radioButton) => {
                    radioButton.addEventListener("change", () => {
                    if (radioButton.id === "COD") {
                        creditCardForm.style.display = "none";
                    } else {
                        creditCardForm.style.display = "block";
                    }
                    });
                });
                </script>

                <hr class="mb-4">
          </form>
        </div>
      </div>

      <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; Pingpong Buffet</p>
        <ul class="list-inline">
          <li class="list-inline-item"><a href="#">Privacy</a></li>
          <li class="list-inline-item"><a href="#">Terms</a></li>
          <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
      </footer>

  </body>
</html>
