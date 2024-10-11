<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Add Category | PPB</title>
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

            // check if the user added a category and click the btncategoryupdate  
            if ((isset($_POST['btnaddcategory'])) && (isset($_SESSION['token']))) {
                
                 // prepare the sql query for checking if category name taken

                 $querycheck = "SELECT categoryname FROM category_tbl WHERE categoryname=?";

                 $statmentcheck = $conn->prepare($querycheck);

                 $statmentcheck->bind_param("s", $_POST['categoryname']);

                 $statmentcheck->execute();

                 $result = $statmentcheck->get_result();

                 if ($result->num_rows > 0) {
                     echo "<div class='alert alert-danger m-t-100 p-b-200 p-t-200' role='alert'>
                                     <center><h4 class='alert-heading'>Unable to process!</h4></center>
                                         <hr>
                                             <center><p class='mb-0'>Sorry, Category name already exist!</p></center>
                                             <center><a href='addcategory.php'><u>Retry Again<u></a></center>
                                 </div>";
                 } else {

                     $datetoday = date('Y-m-d');

                     // prepare sql query for inserting the date in the datebase
                     $q = "INSERT INTO category_tbl (categoryid, categoryname, categorydescription, categorydatecreated, categorylastupdated) 
                             values (?,?,?,?,?)";

                     $statment = $conn->prepare($q);

                     $statment->bind_param(
                         "sssss",
                         $_POST['categoryid'],
                         $_POST['categoryname'],
                         $_POST['categorydescription'],
                         $datetoday,
                         $datetoday
                     );

                     if ($statment->execute() > 0) {
                         echo "<div class='alert alert-success m-t-100 p-b-200 p-t-200' role='alert'>
                                         <center><h4 class='alert-heading'>Successful!</h4></center>
                                             <hr>
                                                 <center><p class='mb-0'>Category successfully added.</p></center>
                                                 <center><a href='categorylist.php'><u>Go to category list Page<u></a></center>
                                     </div>";
                     } else {
                         echo "<div class='alert alert-success m-t-100 p-b-200 p-t-200' role='alert'>
                                         <center><h4 class='alert-heading'>Unsuccessful!</h4></center>
                                             <hr>
                                                 <center><p class='mb-0'>Unable to add category.</p></center>
                                                 <center><a href='addcategory.php'><u>Retry Again<u></a></center>
                                     </div>";
                     }
                 }
                //  end to check if email and password match

                unset($_POST['btnaddcategory']);

                unset($_SESSION['token']);
            } else {
                $_SESSION['token'] = bin2hex(random_bytes(32));

            ?>

            <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="back-link back-backend">
                                    <a href="categorylist.php" class="btn btn-primary">Back to Category List</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div>
                            <div class="col-md-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="text-center custom-login">
                                    <h3 class="text-info" style="font-size:30px;letter-spacing:5px;">Add Category</h3><br>
                                </div>
                                <div class="hpanel">
                                    <div class="panel-body" style="opacity:75%;border-radius:15px;">
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="loginForm" style="padding:20px 20px 70px 20px;">
                                            
                                            <div class="row">

                                            <input type="hidden" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>" name="token">
                                            <input type="hidden" value="<?php echo $uid; ?>" name="uid">

                                                <div class="form-group col-lg-12">
                                                    <label>Category Name</label>
                                                    <input type="text" class="form-control" name="categoryname">
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label>Category Description</label>
                                                    <textarea class="form-control" rows="5" name="categorydescription"></textarea>
                                                    <!-- <input type="text" class="form-control" name="categorydescription" value=""> -->
                                                </div>

                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-success" name="btnaddcategory" style="position:relative; top:40px; right:3px; padding:1% 45% 1% 45%;">Add Category</button>
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


