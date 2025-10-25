<?php
$servername = "localhost";
$user = "root";
$password = "";
$dbname = 'HR3';

$conn = new mysqli($servername, $user, $password, $dbname);

if($conn->connect_error){
  echo "Not connected";
}else{
    // echo "Connected";
}

?>