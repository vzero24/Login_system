<?php
// Include configuration file
include 'config.php';

// Start the session
session_start();

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if user ID is not set
  header('Location: login.php');
  exit;
}

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

// Handle logout request
if (isset($_GET['logout'])) {
  // Clear the user ID from the session
  unset($_SESSION['user_id']);
  session_destroy();
  header('Location: login.php');
  exit;
}

// Query to fetch user information from the database
$sql = "SELECT * FROM `user_form` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user data if available
if ($result->num_rows > 0) {
  $fetch = $result->fetch_assoc();
} else {
  // If no user data found, handle accordingly
  $fetch = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <!-- Link to custom CSS file -->
  <link rel="stylesheet" href="css/style.css?v=1.1">
</head>

<body>
  <div class="container">

    <div class="profile">
      <?php


      // Check if the image field is not empty
      if (empty($fetch['image'])) {
        // If no image is set, display the default image
        echo '<img src="images/default.jpg" alt="Default Profile Image">';
      } else {
        // Construct the image path
        $imagePath = 'uploaded_img/' . htmlspecialchars($fetch['image'], ENT_QUOTES, 'UTF-8');

        // Check if the file actually exists on the server before displaying
        if (file_exists($imagePath)) {
          echo '<img src="' . $imagePath . '" alt="Profile Image">';
        } else {
          echo '<p>Image file does not exist: ' . $imagePath . '</p>';
          echo '<img src="images/default.jpg" alt="Default Profile Image">';
        }
      }
      ?>

      <!-- Display user's name -->
      <h3><?php echo htmlspecialchars($fetch['name'], ENT_QUOTES, 'UTF-8'); ?></h3>

      <!-- Links to update profile and logout -->
      <a href="update_profile.php" class="btn">Update Profile</a>
      <a href="home.php?logout=<?php echo urlencode($user_id); ?>" class="delete-btn">Logout</a>

      <!-- Links to login and register pages -->
      <p>New <a href="login.php">login</a> or <a href="register.php">register</a></p>

    </div>

  </div>

  <!-- For debugging: Display image path in the browser console -->
  <script>
    console.log("Image path: '<?php echo $imagePath; ?>'");
  </script>
</body>

</html>