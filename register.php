<?php
// Include database configuration file
include 'config.php';

// Check if the form has been submitted
if (isset($_POST['submit'])) {
  // Sanitize and retrieve form data
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
  $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

  // Handle file upload
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
  } else {
    $image = ''; // No image uploaded
  }

  // Check if user already exists
  $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('Query failed');

  if (mysqli_num_rows($select) > 0) {
    $message[] = 'User already exists';
  } else {
    // Validate passwords match and image size
    if ($pass != $cpass) {
      $message[] = 'Confirm password does not match';
    } elseif (!empty($image) && $image_size > 2000000) {
      $message[] = 'Image size is too large';
    } else {
      // Insert new user into the database
      $sql = "INSERT INTO `user_form` (`name`, `email`, `password`, `image`) VALUES ('$name', '$email', '$pass', '$image')";
      $insert = mysqli_query($conn, $sql);

      if ($insert) {
        // Move uploaded image to target directory
        if (!empty($image)) {
          move_uploaded_file($image_tmp_name, $image_folder);
        }
        $message[] = 'Registration successful';
        header('Location: login.php');
        exit();
      } else {
        $message[] = 'Registration failed';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- Link to custom CSS file -->
  <link rel="stylesheet" href="css/style.css?v=1.0">
</head>

<body>
  <div class="form-container">
    <form action="register.php" method="POST" enctype="multipart/form-data">
      <h3>Register now</h3>
      <?php
      // Display messages if any
      if (isset($message)) {
        foreach ($message as $msg) {
          echo '<div class="message">' . $msg . '</div>';
        }
        // Clear the message array after displaying
        unset($message);
      }
      ?>
      <!-- Form inputs for user registration -->
      <input type="text" name="name" placeholder="Enter username" class="box" required>
      <input type="email" name="email" placeholder="Enter email" class="box" required>
      <input type="password" name="password" placeholder="Enter password" class="box" required>
      <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" name="submit" value="Register" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
  </div>
</body>

</html>