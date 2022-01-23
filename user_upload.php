<?php 
include('connection.php');

// check for arguments
$shortopts  = "";
$shortopts .= "u";  
$shortopts .= "p"; 
$shortopts .= "h"; 

$longopts  = array(
    "file:",     // Required value
    "help",    // No value
    "dry_run",        // No value
    "create_table",           // No value
);
$options = getopt($shortopts, $longopts);

if (count($options) > 1 && !isset($options["dry_run"])) {
    echo "Too many arguments";
    exit();
} elseif (isset($options["dry_run"]) && !isset($options["file"])) {
    echo "Dry run requires a file";
    exit();
} elseif (isset($options["dry_run"]) && isset($options["file"])) {
     $conn = dbConnect($servername, $username, $password, $dbname);
     createTable($conn);
     $filename = $options["file"];
     dryRun($filename);
     exit();
} elseif (isset($options["create_table"])) {
     $conn = dbConnect($servername, $username, $password, $dbname);
     createTable($conn);
     exit();
} elseif (isset($options["file"])) {
    $conn = dbConnect($servername, $username, $password, $dbname);
    createTable($conn);
    $filename = $options["file"];
    dbInsert($conn, $filename);
    exit();
}
elseif (isset($options["u"]) ) {
   echo "MySQL Username is " . $username;
   exit();  
} elseif (isset($options["p"])) {
    echo "MySQL Password is not available through command line";
    exit(); 
} elseif (isset($options["h"])) {
    echo "MySQL host name is " . $servername;
    exit();
} elseif (isset($options["help"])) {
    echo "You can use the following commands to run the script: \n
    --file 'csv file name' (use double quotes) - this is the name of the CSV to be parsed and will also insert the values into the DB \n
    --create_table - this will cause the MySQL users table to be built (and no further
    action will be taken)\n
    --dry_run - this will be used with the --file directive in case we want to run the script but not \n
    insert into the DB. All other functions will be executed, but the database won't be altered \n
    -u - Displays MySQL username \n
    -p - Displays MySQL password \n
    -h - Displays MySQL host \n
    --help - which will output the above list of directives with details. \n
    ";
    exit();
}


// Returns a connection to the MySQL database
function dbConnect($servername, $username, $password, $dbname) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully\n";
    return $conn;

}


// Creates the users table
function createTable($conn) {

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
}

// Inserts values from the CSV into the DB
function dbInsert($conn, $filename) {
      // Prepare MySQL Statement
$stmt = $conn->prepare('INSERT INTO users (firstname, surname, email) VALUES (?,?,?)');
$stmt->bind_param('sss', $firstnameDB, $lastnameDB, $emailDB);

// iterate through CSV 
$file = fopen($filename,"r");
$row = 1;
$rows_invalid = 0;
$rows_valid = 0;
while (($data = fgetcsv($file)) !== FALSE)
{
    //$charecters = "\n\r\t\v\x00";
   if ($row !== 1) {
    $email = str_replace(" ", "", $data[2]);
    // trim($data[2], "\n\r\t\v\x00");
    if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo $email . " is an invalid email address\nrecord will not be inserted\n";
        $rows_invalid++;            
    } else {
      echo $email . " email valid\n";
      //Remove space and convert to Capitalised 
      $firstname = str_replace(" ", "", $data[0]);
      $firstname =  preg_replace("/[^A-Za-z]/", '', $firstname);
      $firstname = ucwords(strtolower($firstname));
      echo $firstname . " \n"; 

      $surname = str_replace(" ", "", $data[1]);
      $surname =  preg_replace("/[^A-Za-z]/", '', $surname);
      $surname = ucwords(strtolower($surname));
      echo $surname . " \n"; 
      
      //lowercase email
      $email = strtolower($email);
      
      $firstnameDB = $firstname;
      $lastnameDB = $surname;
      $emailDB = $email;
      $stmt->execute();
      $rows_valid++;
    }
}
    //echo $data[0] . $data[1] . $data[2];
    $row++;
}
echo $rows_valid . " Inserted into users \n" . $rows_invalid . " Not inserted due to invalid data type \n";
$stmt->close();
$conn->close();
}

// Prints out the contents of the CSV file and checks for invalid emails
function dryRun($filename) {
    // iterate through CSV 
$file = fopen($filename,"r");
$row = 1;
$invalid_rows = 0;
while (($data = fgetcsv($file)) !== FALSE)
{
    //$charecters = "\n\r\t\v\x00";
   if ($row !== 1) {
    $email = str_replace(" ", "", $data[2]);
    // trim($data[2], "\n\r\t\v\x00");
    if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo $email . " is an invalid email address\n";
         $invalid_rows++;           
    } else {
      echo $email . " email valid\n";
      //Remove space and convert to Capitalised 
      $firstname = str_replace(" ", "", $data[0]);
      $firstname =  preg_replace("/[^A-Za-z]/", '', $firstname);
      $firstname = ucwords(strtolower($firstname));
      echo $firstname . " \n"; 

      $surname = str_replace(" ", "", $data[1]);
      $surname =  preg_replace("/[^A-Za-z]/", '', $surname);
      $surname = ucwords(strtolower($surname));
      echo $surname . " \n"; 
      
      //lowercase email
      $email = strtolower($email);
      
    }
}
    //echo $data[0] . $data[1] . $data[2];
    $row++;
}
echo "There were " . $invalid_rows . " invalid rows\n";
$stmt->close();
$conn->close();
}

// // Prepare MySQL Statement
// $stmt = $conn->prepare('INSERT INTO users (firstname, surname, email) VALUES (?,?,?)');
// $stmt->bind_param('sss', $firstnameDB, $lastnameDB, $emailDB);

// // iterate through CSV 
// $file = fopen($filename,"r");
// $row = 1;

// while (($data = fgetcsv($file)) !== FALSE)
// {
//     //$charecters = "\n\r\t\v\x00";
//    if ($row !== 1) {
//     $email = str_replace(" ", "", $data[2]);
//     // trim($data[2], "\n\r\t\v\x00");
//     if (!Filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         echo $email . " is an invalid email address\nrecord will not be inserted\n";
                    
//     } else {
//       echo $email . " email valid\n";
//       //Remove space and convert to Capitalised 
//       $firstname = str_replace(" ", "", $data[0]);
//       $firstname =  preg_replace("/[^A-Za-z]/", '', $firstname);
//       $firstname = ucwords(strtolower($firstname));
//       echo $firstname . "i \n"; 

//       $surname = str_replace(" ", "", $data[1]);
//       $surname =  preg_replace("/[^A-Za-z]/", '', $surname);
//       $surname = ucwords(strtolower($surname));
//       echo $surname . "i \n"; 
      
//       //lowercase email
//       $email = strtolower($email);
      
//       $firstnameDB = $firstname;
//       $lastnameDB = $surname;
//       $emailDB = $email;
//       $stmt->execute();
//     }
// }
//     //echo $data[0] . $data[1] . $data[2];
//     $row++;
// }
// $stmt->close();
// $conn->close();