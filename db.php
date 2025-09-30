<?php
$hostname="localhost";
$username="root";
$password="1234";
$database="evoting_system";
$conn=mysqli_connect($hostname,$username,$password,$database);
if(!$conn){
    die("Connection Failed: ".mysqli_connect_error());
}
else{
     // echo "Database Connected Successfully";
}
?>