<?php

include('includes/connection.php');

function getAll($table){

    global $conn;
    $q = "SELECT * FROM $table";
    return $query_run = mysqli_query($conn,$q);
    
}


function redirect($url, $message){

    $_SESSION['message'] = $message;
    header('Location:' . $url);
    exit();
}

?>