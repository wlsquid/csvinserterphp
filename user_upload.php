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
    surname VARCHAR(100) NOT NULL,
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

// Prepare MySQL Statement
$stmt = $conn->prepare('INSERT INTO users (firstname, surname, email) VALUES (?,?,?)');
$stmt->bind_param('sss', $firstnameDB, $lastnameDB, $emailDB);

// iterate through CSV 
$file = fopen("users.csv","r");
$row = 1;

while (($data = fgetcsv($file)) !== FALSE)
{
    //$charecters = "\n\r\t\v\x00";
   if ($row !== 1) {
    $email = str_replace(" ", "", $data[2]);
    // trim($data[2], "\n\r\t\v\x00");
    if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo $email . " is an invalid email address\nrecord will not be inserted\n";
                    
    } else {
      echo $email . " email valid\n";
      //Remove space and convert to Capitalised 
      $firstname = str_replace(" ", "", $data[0]);
      $firstname =  preg_replace("/[^A-Za-z]/", '', $firstname);
      $firstname = ucwords(strtolower($firstname));
      echo $firstname . "i \n"; 

      $surname = str_replace(" ", "", $data[1]);
      $surname =  preg_replace("/[^A-Za-z]/", '', $surname);
      $surname = ucwords(strtolower($surname));
      echo $surname . "i \n"; 
      
      //lowercase email
      $email = strtolower($email);
      
      $firstnameDB = $firstname;
      $lastnameDB = $surname;
      $emailDB = $email;
      $stmt->execute();
    }
}
    //echo $data[0] . $data[1] . $data[2];
    $row++;
}
$stmt->close();
$conn->close();