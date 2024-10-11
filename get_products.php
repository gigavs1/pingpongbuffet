<!-- display the product -->
<?php

  include('includes/connection.php');

  session_start();

  if (isset($_GET['categoryid'])) {
    $categoryid = $_GET['categoryid'];


    $q = "SELECT product_tbl.*, category_tbl.categoryname
          FROM product_tbl
          INNER JOIN category_tbl ON product_tbl.categoryid = category_tbl.categoryid
          WHERE product_tbl.categoryid = ?";

    $statement = $conn->prepare($q);
    $statement->bind_param("s", $categoryid);
    $statement->execute();
    $res = $statement->get_result();

    if ($res->num_rows > 0) {
      // display the category name for the first row
      $firstRow = $res->fetch_assoc();
      $currentCategory = $firstRow['categoryname'];
      echo "<div class='tab-content' data-aos='fade-up' data-aos-delay='300'>
            <div class='ab-pane fade active show' id='menu-starters'>
              <div class='tab-header text-center'>
                <p>Menu</p>
                <h3>" . $currentCategory . "</h3>
              </div>";
    
      echo "<div class='row gy-5'>";
    
      // display the rest of the products
      do {
        ?>
    
        <div class="col-lg-4 menu-item">
          <a href="uploads/<?php echo $firstRow['productimage']; ?>" class="glightbox">
            <img src="uploads/<?php echo $firstRow['productimage']; ?>" height="300px" width="300px" class="menu-img img-fluid" alt="<?php echo $firstRow['productname']; ?>">
          </a>
          <h4><?php echo $firstRow['productname']; ?></h4>
          <p class="ingredients"><?php echo $firstRow['productdescription']; ?></p>
          <p class="price"><?php echo "PHP " . number_format($firstRow['productprice'], 2, '.', ','); ?></p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="productname" value="<?php echo $firstRow['productname']; ?>">
            <input type="hidden" name="productprice" value="<?php echo $firstRow['productprice']; ?>">
            <input type="hidden" name="productquantity" value="1">
            <button type="submit" class="btn btn-danger" name="addtocart">Add to Cart</button>
          </form>
        </div>
    
        <?php
      } while ($firstRow = $res->fetch_assoc());
    
      echo "</div></div></div>";
    } else {
      echo "No products found.";
    }
    
  } else {
    echo "<script>setTimeout(function() { window.location.href = 'index.php#menu'; }, 1);</script>";
      
  }

  $_SESSION['token'] = bin2hex(random_bytes(32));

  if (isset($_POST['addtocart'])) {
    
    include('includes/connection.php');
     
    $user_id = $_SESSION['user_id'];
    $product_name = $_POST['productname'];
    $product_price = $_POST['productprice'];
    $product_quantity = $_POST['productquantity'];

    // Check if the product already exists in the cart
    $check_q = "SELECT productquantity FROM cart_tbl WHERE userid = ? AND productname = ? AND productprice = ?";
    $check_statement = $conn->prepare($check_q);
    $check_statement->bind_param("sss", $user_id, $product_name, $product_price);
    $check_statement->execute();
    $check_statement->store_result();

    if ($check_statement->num_rows > 0) {
        // Update the product quantity if it already exists in the cart
        $update_q = "UPDATE cart_tbl SET productquantity = productquantity + ? WHERE userid = ? AND productname = ? AND productprice = ?";
        $update_statement = $conn->prepare($update_q);
        $update_statement->bind_param("ssss", $product_quantity, $user_id, $product_name, $product_price);
        $update_statement->execute();
        $update_statement->close();
        echo "<script>alert('" . $product_name . " quantity updated successfully!')</script>";
    } else {
        // Insert the product into the cart if it does not already exist
        $insert_q = "INSERT INTO cart_tbl (userid, productname, productprice, productquantity) VALUES (?, ?, ?, ?)";
        $insert_statement = $conn->prepare($insert_q);
        $insert_statement->bind_param("ssss", $user_id, $product_name, $product_price, $product_quantity);
        if ($insert_statement->execute()) {
            echo "<script>alert('" . $product_name . " added to cart successfully!')</script>";
        } else {
            echo "Product not found!";
        }
        $insert_statement->close();
    }
    $check_statement->close();

    // Count the number of products in the cart
    $count_q = "SELECT SUM(productquantity) FROM cart_tbl WHERE userid = ?";
    $count_statement = $conn->prepare($count_q);
    $count_statement->bind_param("s", $user_id);
    $count_statement->execute();
    $count_statement->bind_result($product_count);
    $count_statement->fetch();
    $count_statement->close();

    // Update the badge number
    if ($product_count == 0) {
        setcookie('cart_count', 0, strtotime('+10 years'), '/');
    } else {
        setcookie('cart_count', $product_count, strtotime('+10 years'), '/');
    }

    $conn->close();
}

?>
