<?php


$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "esports_management"; 

/*
$servername = "sql308.infinityfree.com";  
$username = "if0_38801586";         
$password = "esports62042";             
$dbname = "if0_38801586_esports_management";
*/

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
