<?php
ob_start();
?>

<style>
    <?php include 'styles.css'; ?>
</style>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        var editButtons = document.querySelectorAll('.edit-btn');
        var updateButtons = document.querySelectorAll('.update-form-btn');
        var deleteButtons = document.querySelectorAll(".delete-btn");
        var cancelButtons = document.querySelectorAll(".cancel-btn")
        // Add click event listener to all Edit buttons
        editButtons.forEach(function (editButton, index) {
            var formFields = editButton.parentNode.parentNode.querySelectorAll('.editable-cell input');

            editButton.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent the form submission

                var isEditing = editButton.textContent === 'Edit';

                // Toggle the visibility of the buttons
                editButton.style.display = isEditing ? 'none' : 'inline';
                deleteButtons[index].style.display = isEditing ? 'none' : 'inline';
                updateButtons[index].style.display = isEditing ? 'inline' : 'none';
                cancelButtons[index].style.display = isEditing ? 'inline' : 'none';

                // Toggle the readonly attribute of the form fields
                formFields.forEach(function (field) {
                    field.readOnly = !isEditing;
                });
                cancelButtons[index].addEventListener('click', function () {
                    formFields.forEach(function (field) {
                        isEditing=false
                        field.readOnly = !isEditing;
                        editButton.style.display = isEditing ? 'none' : 'inline';
                        deleteButtons[index].style.display = isEditing ? 'none' : 'inline';
                        updateButtons[index].style.display = isEditing ? 'inline' : 'none';
                        cancelButtons[index].style.display = isEditing ? 'inline' : 'none';

                    });
                })
            });
        });
    });
</script>

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

if (!empty($_GET['id'])) {

    ?>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">

        <div class="popup">
            <h3 class="confirm-text">Are you sure you want to delete this record?</h3>
            <button type="submit" class="button yes" name="confirm-btn" value="yes"><i
                    class="fa-regular bin fa-trash-can fa-lg" style="color: #ffffff;"></i>Delete</button>
            <button type="submit" class="button no" name="confirm-btn" value="no">Cancel</button>
        </div>
        <div class="overlay">
        </div>
        <style>
            body {
                overflow: hidden;
            }
        </style>
    </form>
    <?php
    if (isset($_POST['confirm-btn'])) {
        $choice = $_POST['confirm-btn'];
        if ($choice === "yes") {
            $id = $_GET['id'];
            $sql = "DELETE FROM mytable WHERE id = '$id';";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                mysqli_close($conn);
                $deleteMessage = "Deletion process has been carried out succesfully! ";
            }
            echo '<script>';
            echo 'var confirmation = confirm(' . json_encode($deleteMessage) . ');';
            echo 'if (confirmation) {';
            echo '  window.location.href = "http://localhost/demo/index.php";';
            echo '}';
            echo '</script>';
        } else {
            // Redirect after user confirmation
            header("Location:http://localhost/demo/index.php ");
            exit;
        }

    }
}

//removing any extra spaces and preventing scripts to be injected
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// checking for the empty fields in the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'])) {
        // to print the error messages
        if (empty($_POST['name'])) {
            $nameerr = "NAME IS REQUIRED";
        } else {
            $name = test_input($_POST['name']);
        }
        if (empty($_POST['email']))
            $emailerr = "EMAIL IS REQUIRED";
        else {
            $email = test_input($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailerr = "Invalid email format";
                $email = "";
            }
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

        //checking all required fields are filled before sending the query to db

        if (!empty($name) && !empty($parentName) && !empty($email) && !empty($gender) && !empty($dob)) {

            $sql = "INSERT INTO mytable (name, parentName,email,gender,dob,comment) VALUES('$name', '$parentName', '$email', '$gender', '$dob', '$comment')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $successMessage = "Form submitted successfully! Thank you for your submission.";

            // Redirect after user confirmation
            echo '<script>';
            echo 'var confirmation = confirm(' . json_encode($successMessage) . ');';
            echo 'if (confirmation) {';
            echo '  window.location.href = "http://localhost/demo/index.php";';
            echo '}';
            echo '</script>';
            // header("Location:http://localhost/demo/index.php ");
            exit;
        }
    }

    if (isset($_POST['form2_submitted'])) {
        if (
            !empty($_POST['id']) && !empty($_POST['name-update']) && !empty($_POST['parentName']) && !empty($_POST['email'])
            && !empty($_POST['gender']) && !empty($_POST['dob'])
        ) {
            $id = $_POST['id'];
            $names = $_POST['name-update'];
            $parentNames = $_POST['parentName'];
            $emails = $_POST['email'];
            $genders = $_POST['gender'];
            $dobs = $_POST['dob'];
            $comments = $_POST['comment'];



            // Loop through the submitted data and update the database
            $name_val = test_input($names[0]);
            $parentName_val = test_input($parentNames[0]);
            $email_val = test_input($emails[0]);
            if (!filter_var($email_val, FILTER_VALIDATE_EMAIL)) {

                $email_val = "";
            }
            $gender_val = test_input($genders[0]);
            $gender_val = strtolower($gender_val);

            // Check if the gender is either "male" or "female"
            if ($gender_val !== "male" && $gender_val !== "female") {
                $gender_val = "";
            }

            $dob_val = test_input($dobs[0]);
            $comment_val = test_input($comments[0]);

            if (!empty($name_val) && !empty($parentName_val) && !empty($email_val) && !empty($gender_val) && !empty($dob_val)) {
                // Perform the update query here
                $sql = "UPDATE mytable SET name='$name_val', parentName='$parentName_val', email='$email_val', gender='$gender_val', dob='$dob_val', comment='$comment_val' WHERE id='$id[0]'";
                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }


                $conn->close();
                $updateMessage = "Form has been updated successfully! Thank you for your submission.";

                // Redirect after user confirmation
                echo '<script>';
                echo 'var confirmation = confirm(' . json_encode($updateMessage) . ');';
                echo 'if (confirmation) {';
                echo '  window.location.href = "http://localhost/demo/index.php";';
                echo '}';
                echo '</script>';
                // header("Location:http://localhost/demo/index.php ");
                exit;
            }
        }
    }
}


?>

<!-- join-section -->
<div id="join-section">
    <div class="left">
        <h1 class="main-heading gradient">Join the School today</h1>
        <a class="button" href="#form" target="_blank">Register Here</a>
        <h3>Individual classes</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa enim id est</p>
    </div>
    <div class="right">
        <img src="imgs/teenage-student-headphones-sitting-table-typing-notebook (1).jpg" alt="">
    </div>
</div>

<div class="gradient-div "></div>

<div id="form">
    <div class="form-section">
        <form class="main-form" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <h3>Join for success✨</h3>
            <p>Are you ready to take the next step towards a successfull future? Look no further than CodeSchool</p>
            <label for="name">Full name</label>
            <span class="error">*
                <?php echo $nameerr ?>
            </span>
            <br>
            <input type="text" placeholder="Enter your Full name" value="<?php echo $name ?>" name="name">
            <br>
            <br>
            <label for="parentname">Parent name</label>
            <span class="error">*
                <?php echo $parentNameerr ?>
            </span>
            <br>
            <input type="text" name="parentName" placeholder="Enter your Parent's name"
                value="<?php echo $parentName ?>">
            <br>
            <br>
            <label for="">Date of birth</label>
            <span class="error">*
                <?php echo $doberr ?>
            </span>
            <br>
            <input type="date" name="dob" value="<?php echo $dob ?> ">
            <br>
            <br>
            <label for="">Email</label>
            <span class="error">*
                <?php echo $emailerr ?>
            </span>
            <br>
            <input type="email" name="email" placeholder="Enter your email" id="" value="<?php echo $email ?> ">
            <br>
            <br>
            <label for="gender">Gender</label>
            <span class="error">*
                <?php echo $gendererr ?>
            </span>
            <br>
            <input type="radio" name="gender" class="gender" value="Female" id=""><span class="gender">Female</span>
            <input type="radio" name="gender" value="Male" id=""> <span class="gender">Male</span>
            <input type="radio" name="gender" value="Others" id=""><span class="gender">Others</span>
            <br>
            <br>
            <label for="remarks">Remarks</label>
            <br>
            <textarea name="comment" placeholder="Any additional Remark/Comment goes here "> </textarea>
            <br>
            <input class="submit" type="submit" value="Submit">
        </form>

        <img src="imgs/portrait-of-smiling-male-student-in-corridor-of-university-MASF19099.jpg" alt="">
    </div>
</div>

<h1>FETCHING STUDENT DETAILS FROM THE DB</h1>
<br><br>
<!--creating table -->
<?php
$conn = mysqli_connect($servername, $username, $password, $db);
$sql = "SELECT * FROM `mytable`";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    ?>
    <table>
        <thead>
            <!-- ... Your existing table header ... -->
            <tr>
                <th>Name </th>
                <th>Parent Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Date of birth</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <form class='table-form'action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                        <input type='hidden' name='form2_submitted' value='1'>
                        <td class='editable-cell'>
                            <input type='hidden' name='id[]' value='" . $row["id"] . "'>
                            <input type='text'  value='" . $row["name"] . "' name='name-update[]' readonly>
                        </td>
                        <td class='editable-cell'>
                        <div class=input-div>
                            <input type='text'  value='" . $row["parentName"] . "' name='parentName[]' readonly>
                        </div>
                        </td>
                        <td class='editable-cell'>
                            <input type='email'  value='" . $row["email"] . "' name='email[]' readonly>
                        </td>
                        <td class='editable-cell'>
                            <input type='text'  value='" . $row["gender"] . "' name='gender[]' readonly>
                        </td>
                        <td class='editable-cell'>
                            <input type='date'  value='" . $row["dob"] . "' name='dob[]' readonly>
                        </td>
                        <td class='editable-cell'>
                            <input type='text'  value='" . $row["comment"] . "' name='comment[]' readonly>
                        </td>
                        <td class='editable-cell'>
                            <a class='delete-btn table button ' href='index.php?id=" . $row["id"] . "'>Delete</a> 
                            <button type='button' class='cancel-btn table button' style='display:none;'>Cancel</button>                           
                            <button type='button' class='edit-btn table button'>Edit</button>
                            <input type='submit' class='update-form-btn table button' style='display:none;' value='Update'>

                        </td>
                    </form>
                </tr>";
            }
            echo "</table>";
} else {
    ?>
            <h3>No Students enrolled at the moment.<h3>
                    <h4>Be the first Student to avail early discounts</h4>
                    <?php
}
?>
                </body>

                </html>