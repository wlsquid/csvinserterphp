<?php 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "csvinserter";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

$exists = $conn->query("SELECT 1 from users LIMIT 1");

if ($exists === FALSE) {

$sql = "CREATE TABLE users (
    id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    surename VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) ===  TRUE) {
    echo "Table 'users' was created\n";
} else {
    echo "Error creating table: " . $conn->error;
    exit();
} 

} else {
    echo "users already exists\n";
}

// iterate through CSV 
$file = fopen("users.csv","r");

while (($data = fgetcsv($file)) !== FALSE)
{
    $charecters = "\n\r\t\v\x00";

    $email = str_replace(" ", "", $data[2]);
    
    // trim($data[2], "\n\r\t\v\x00");
    if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo $email . " is an invalid email address\n";
    } else {
      echo "email valid\n";
    }
    //echo $data[0] . $data[1] . $data[2];
}

$conn->close();