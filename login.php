<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

  // Correct SQL syntax: use backticks around table name and column names
  $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE 
  email = '$email' AND password = '$pass'") or die('query failed');

  if (mysqli_num_rows($select) > 0) {
    $row = mysqli_fetch_assoc($select);
    $_SESSION['user_id'] = $row['id'];
    header('Location: home.php');
    exit(); // Stop further script execution
  } else {
    $message[] = 'Incorrect email or password!';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- custom css file link -->
  <link rel="stylesheet" href="css/style.css?v=1.0">
</head>

<body>
  <div class="form-container">
    <form action="login.php" method="POST" enctype="multipart/form-data">
      <h3>Login now</h3>
      <?php
      if (isset($message)) {
        foreach ($message as $msg) {
          echo '<div class="message">' . $msg . '</div>';
        }
        // Clear the entire $message array after displaying the messages
        unset($message);
      }
      ?>
      <input type="email" name="email" placeholder="enter email" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="submit" name="submit" value="Login" class="btn">
      <p>don't have an account ? <a href="register.php">register now</a></p>
    </form>
  </div>
</body>

</html>