<?php 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "csvinserter";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$sql = "CREATE TABLE users (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    surename VARCHAR(100) NOT NULL,
    email VARCHAR(320) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) ===  TRUE) {
    echo "Table users was created";
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

// iterate through CSV 
$file = fopen("users.csv","r");

while (($data = fgetcsv($file)) !== FALSE)
{
    $email = $data[2];
    if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo $email . " is an invalid email address";
    } else {
      echo "email valid";
    }
    //echo $data[0] . $data[1] . $data[2];
}

$conn->close();