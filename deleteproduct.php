<?php 

    //database connection
    include('includes/connection.php');

    //prepare for query delete
    $querydel  = "DELETE FROM product_tbl  WHERE productid=?";
    $st = $conn->prepare($querydel);
    $st->bind_param("s", $_GET['delid']);
    $st->execute();

?>