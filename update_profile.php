<?php
// Include the database configuration file
include 'config.php';
// Start the session to access user information
session_start();

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

// Initialize an array to hold messages for the user
$message = [];

// Check if the 'update_profile' form has been submitted
if (isset($_POST['update_profile'])) {

  // Sanitize and retrieve the updated name and email from the form
  $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
  $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

  // Update the user's name and email in the database
  $update_profile_query = "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$user_id'";
  mysqli_query($conn, $update_profile_query) or die('Query failed: ' . mysqli_error($conn));

  // Retrieve and hash the old, new, and confirm password fields
  $old_pass = isset($_POST['old_pass']) ? md5($_POST['old_pass']) : ''; // Old password (already hashed)
  $update_pass = isset($_POST['update_pass']) ? md5($_POST['update_pass']) : ''; // Old password from user input
  $new_pass = isset($_POST['new_pass']) ? md5($_POST['new_pass']) : ''; // New password from user input
  $confirm_pass = isset($_POST['confirm_pass']) ? md5($_POST['confirm_pass']) : ''; // Confirmed new password
  // Check if any of the password fields are filled
  if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {

    // Fetch the current password from the database
    $current_pass_query = "SELECT password FROM `user_form` WHERE id = '$user_id'";
    $result = mysqli_query($conn, $current_pass_query) or die('Query failed: ' . mysqli_error($conn));
    $row = mysqli_fetch_assoc($result);

    // Check if the provided old password matches the one in the database
    if ($update_pass != $row['password']) {
      $message[] = 'Old password does not match.';
    }
    // Check if the new password and confirm password fields match
    elseif ($new_pass != $confirm_pass) {
      $message[] = 'New password and confirm password do not match.';
    }
    // If both conditions pass, update the password in the database
    else {
      $update_password_query = "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$user_id'";
      mysqli_query($conn, $update_password_query) or die('Query failed: ' . mysqli_error($conn));
      $message[] = 'Password updated successfully.';
    }
  }

  // Handle the profile image update
  $update_image = $_FILES['update_image']['name'];
  $update_image_size = $_FILES['update_image']['size'];
  $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
  $update_image_folder = 'uploaded_img/' . $update_image;

  // Check if an image is provided
  if (!empty($update_image)) {
    // Ensure the image size does not exceed 2MB
    if ($update_image_size > 2000000) {
      $message[] = 'Image file size should not exceed 2MB';
    }
    // If valid, update the image in the database
    else {
      $update_image_query =  "UPDATE `user_form` SET image = '$update_image' WHERE id = '$user_id'";
      $image_update_query = mysqli_query($conn, $update_image_query) or die('Query failed: ' . mysqli_error($conn));
      if ($image_update_query) {
        move_uploaded_file($update_image_tmp_name, $update_image_folder); // Move the image to the correct folder
      }
      $message[] = 'Image updated successfully';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Profile</title>
  <!-- Link to custom CSS file -->
  <link rel="stylesheet" href="css/style.css?v=1.1">
</head>

<body>
  <div class="update-profile">
    <?php
    // Fetch the user's current profile information
    $fetch_query = "SELECT * FROM `user_form` WHERE id = '$user_id'";
    $select = mysqli_query($conn, $fetch_query) or die('Query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($select) > 0) {
      $fetch = mysqli_fetch_assoc($select); // Fetch user data as an associative array
    }
    ?>

    <!-- Profile update form -->
    <form action="" method="post" enctype="multipart/form-data">
      <?php
      // Display the user's profile picture, or a default one if not available
      if ($fetch['image'] == '') {
        echo '<img src="images/default.jpg" alt="Default Profile Picture">';
      } else {
        echo '<img src="uploaded_img/' . $fetch['image'] . '" alt="User Profile Picture">';
      }

      // Display any messages (e.g., password mismatch, image update)
      if (!empty($message)) {
        foreach ($message as $msg) {
          echo '<div class="message">' . $msg . '</div>';
        }
        // Clear the message array after displaying
        $message = [""];
      }
      ?>

      <div class="flex">
        <div class="inputbox">
          <span>Username:</span>
          <input type="text" name="update_name" value="<?php echo htmlspecialchars($fetch['name']); ?>" class="box">
          <span>Your Email:</span>
          <input type="email" name="update_email" value="<?php echo htmlspecialchars($fetch['email']); ?>" class="box">
          <span>Update Your Picture:</span>
          <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
        </div>

        <div class="inputbox">
          <!-- Hidden field to pass the old password for validation -->
          <input type="hidden" name="old_pass" value="<?php echo htmlspecialchars($fetch['password']); ?>">
          <span>Old Password:</span>
          <input type="password" name="update_pass" placeholder="Enter previous password" class="box">
          <span>New Password:</span>
          <input type="password" name="new_pass" placeholder="Enter new password" class="box">
          <span>Confirm Password:</span>
          <input type="password" name="confirm_pass" placeholder="Confirm password" class="box">
        </div>
      </div>

      <!-- Submit and back buttons -->
      <input type="submit" value="Update Profile" name="update_profile" class="btn">
      <a href="home.php" class="delete-btn">Go Back</a>
    </form>
  </div>
</body>

</html>