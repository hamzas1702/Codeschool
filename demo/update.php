<style>
  <?php include 'styles.css'; ?>
</style>

<?php
include 'header.html';
$servername = "localhost";
$username = "root";
$password = "";
$db = "demo";

$nameerr = $emailerr = $gendererr = $parentNameerr = $doberr = "";
$name = $email = $gender = $parentName = $dob = $comment = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 

//getting values of id to be updated in the db 
$id=$_POST['id'];

//removing any extra spaces nd  preventing scripts to be injected
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// checking for the empty fields in the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (empty($_POST['name-update'])) {
        $nameerr = "NAME IS REQUIRED";
    } else {
        $name = test_input($_POST['name-update']);
    }
    if (empty($_POST['email']))
        $emailerr = "EMAIL IS REQUIRED";
    else {
        $email = test_input($_POST['email']);
    }
    if (empty($_POST['parentName']))
        $parentNameerr = "PARENTNAME IS REQUIRED";
    else {
        $parentName = test_input($_POST['parentName']);
    }
    if (empty($_POST['gender']))
        $gendererr = "GENDER IS REQUIRED";
    else {
        $gender = test_input($_POST['gender']);
    }
    if (empty($_POST['dob']))
        $doberr = "DATE OF BIRTH IS REQUIRED";
    else {
        $dob = test_input($_POST['dob']);
    }
    $comment = test_input($_POST['comment']);


    //checking all required fields are filled before sending  the query to db

    
   

}




?>

