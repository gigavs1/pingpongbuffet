<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Update User | PPB</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="admin/img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/owl.carousel.css">
    <link rel="stylesheet" href="admin/css/owl.theme.css">
    <link rel="stylesheet" href="admin/css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/normalize.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/main.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="admin/css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="admin/css/calendar/fullcalendar.print.min.css">
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/form/all-type-forms.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="admin/style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="admin/css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="admin/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>

            <!-- start of inserting category on the database -->
            <?php

            session_start();

            include('includes/connection.php');
            include('functions/functions.php');

            if ((isset($_POST['btnupdateproduct'])) && (isset($_SESSION['token']))) {

                //  start of updating category
                $datetoday = date('Y-m-d');
                $path = dirname(__FILE__) . "/uploads";

                $new_img = $_FILES['image']['name'];
                $old_img = $_POST['old_image'];

                if ($new_img != "") {
                  $image_ext = pathinfo($new_img, PATHINFO_EXTENSION);
                  $filename = time(). '.' . $image_ext;
                }else {
                  $filename = $old_img;
                }
                
                $_POST['image'] = $filename;

                // prepare sql query for inserting the data in the datebase
                $q = "UPDATE product_tbl SET categoryid=?, productname=?, productdescription=?, productprice=?, productimage=? , quantity=?, dateupdated=? WHERE productid=?";
        
                $statment = $conn->prepare($q);
        
                $statment->bind_param("ssssssss",$_POST['categoryid'],$_POST['productname'],$_POST['productdescription'],
                $_POST['productprice'],$_POST['image'],$_POST['productquantity'],$datetoday,$_POST['pid']);
        
                if ($statment->execute()>0) {
                  
                  if ($_FILES['image']['name'] != "") {
                    move_uploaded_file($_FILES['image']['tmp_name'], $path. '/' .$filename);
                    if (file_exists("./uploads/".$old_img)) {
                      unlink("./uploads/". $old_img);
                    }
                  }
                  echo "<div class='alert alert-success' role='alert'>
                          <center><h4 class='alert-heading'>Successful!</h4></center>
                            <hr>
                              <center><p class='mb-0'>Your data has been successfully updated.</p></center>
                              <center><a href='productlist.php'><u>Go back to Product List<u></a></center>
                        </div>";
                } else {
                  echo "<div class='alert alert-danger' role='alert'>
                          <center><h4 class='alert-heading'>Unsuccessful!</h4></center>
                            <hr>
                              <center><p class='mb-0'>Unable to updated your data.</p></center>
                              <center><a href='editproduct.php'><u>Retry Again<u></a></center>
                        </div>";
                }
                //  end of updating users info
            
                unset($_POST['btncategoryupdate']);
            
                unset($_SESSION['token']);
        
              } else {

                $pid = (int)$_GET['pid']; 

                $q = "SELECT * FROM product_tbl WHERE productid=?";
      
                $stmt1 = $conn->prepare($q);
                $stmt1->bind_param("s", $pid);
                $stmt1->execute();
                $res = $stmt1->get_result();
                if ($res->num_rows>0) {
      
                  $rows = $res->fetch_assoc();
      
                }

                $_SESSION['token'] = bin2hex(random_bytes(32));

            ?>

            <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="back-link back-backend">
                                    <a href="productlist.php" class="btn btn-primary">Back to Product List</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div>
                            <div class="col-md-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="text-center custom-login">
                                    <h3 class="text-info" style="font-size:30px;letter-spacing:5px;">Edit Product</h3><br>
                                </div>
                                <div class="hpanel">
                                    <div class="panel-body" style="opacity:75%;border-radius:15px;">
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="loginForm" enctype="multipart/form-data" style="padding:10px 20px 10px 20px;position:relative;top:30%;">
                                            
                                            <div class="row">

                                            <input type="hidden" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>" name="token">
                                            <input type="hidden" value="<?php echo $pid; ?>" name="pid">

                                            <div class="form-group col-lg-12">
                                                    <label>Category id</label>
                                                    <select name="categoryid" class="form-control">
                                                        <option selected>Select Category</option>
                                                        <?php 
                                                            $category = getAll("category_tbl");
                                                            if (mysqli_num_rows($category) > 0) {
                                                                foreach ($category as  $item) {
                                                                    ?>
                                                                      <option value="<?php echo $item['categoryid'] ?>" <?php echo $rows['categoryid'] == $item['categoryid']?'selected':''?>><?php echo $item['categoryname'] ?></option>
                                                                    <?php
                                                                }
                                                            }else {
                                                                echo "No Category Available";
                                                            }
                                                        ?>

                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label>Product Name</label>
                                                    <input type="text" class="form-control" name="productname" value="<?php echo $rows['productname']?>" placeholder="Enter Product Name">
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label>Product Description</label>
                                                    <textarea class="form-control" rows="3" name="productdescription"  placeholder="Enter Product Description"><?php echo $rows['productdescription']?></textarea>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>Product Image</label>
                                                    <input type="hidden" name="old_image" value="<?php echo $rows['productimage']?>">
                                                    <input type="file" class="form-control" name="image" placeholder="Select Product Image">
                                                    
                                                    <label>Product Image</label>
                                                    <img src="./uploads/<?php echo $rows['productimage']?>" alt="Product Image" width="50px" height="50px">
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>Product Price</label>
                                                    <input type="text" class="form-control" name="productprice" value="<?php echo $rows['productprice']?>" placeholder="Enter Product Price">
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>Product Quantity</label>
                                                    <input type="text" class="form-control" name="productquantity" value="<?php echo $rows['quantity']?>" placeholder="Enter Product Quantity">
                                                </div>

                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-success" name="btnupdateproduct" style="position:relative; top:10px; right:3px; padding:1% 45% 1% 45%;">Update Product</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                </div>
                            </div> 
                          </div>
                
                        <div class="footer-copyright-area" style="position: fixed; bottom: 0; left: 0; right: 0;">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="footer-copy-right">
                                            <p>Copyright © 2023
                                                <a href="index.php"><strong>Pingpong Buffet</strong></a> All rights reserved.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            <?php
            }
            ?>
            <!-- end inserting category data on database -->

    <!-- jquery
		============================================ -->
    <script src="admin/js/vendor/jquery-1.11.3.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="admin/js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="admin/js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="admin/js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="admin/js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="admin/js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="admin/js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="admin/js/jquery.scrollUp.min.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="admin/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="admin/js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="admin/js/metisMenu/metisMenu.min.js"></script>
    <script src="admin/js/metisMenu/metisMenu-active.js"></script>
    <!-- tab JS
		============================================ -->
    <script src="admin/js/tab.js"></script>
    <!-- icheck JS
		============================================ -->
    <script src="admin/js/icheck/icheck.min.js"></script>
    <script src="admin/js/icheck/icheck-active.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="admin/js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="admin/js/main.js"></script>
</body>

</html>


