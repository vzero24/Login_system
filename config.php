<?php
// $conn = mysqli_connect('localhost', 'root', '', 'user_db') or die('connection failed');
$conn = mysqli_connect('db', 'root', 'root_password', 'user_db') or die('connection failed');


// the following is the connection structure for dockerfile

// Database connection parameters
// $servername = "db";  // Use the service name 'db' from docker-compose.yml
// $username = "root";  // The user you defined in docker-compose.yml
// $password = "";  // The password you defined in docker-compose.yml
// $dbname = "user_db";  // The database name you defined in docker-compose.yml

// // Create connection
// $conn = mysqli_connect($servername, $username, $password, $dbname);

// // Check the connection
// if (!$conn) {
//   die('Connection failed: ' . mysqli_connect_error());
// }
