<?php
    $servername = "localhost";
    $username = "onlinefood";
    $password = "abc1234";
    $dbname = "onlinefoodstore";

    $conn = new mysqli($servername, $username, $password, $dbname);
    // if($conn->connect_error){
    //     die("Connection Eeeoe: "+$conn->connect_error);
    // }
    // echo "connection success <br>";
?>