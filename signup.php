<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST'
      && isset($_POST['username'])
      && isset($_POST['password'])
      && isset($_POST['address']) && isset($_POST['phone']))
  {
      require_once('config.php');
      $pg = "SELECT * FROM users INNER JOIN agencies ON users.agencyid = agencies.agencyid WHERE users.username = '" . trim($_POST['username']) . "' OR agencies.agencyaddress = '" . trim($_POST['address']) . "';";
      $result = pg_query($link, $pg);
      if (pg_num_rows($result) > 0) {
         echo '<div class="login-error">Signup failed. Username or address or phone is invalid.</div>';
      }
      else{
        $pg = "INSERT INTO agencies(agencyaddress, agencyphone) VALUES ('" . trim($_POST['address']) . "','" . trim($_POST['phone']) . "');";
        $result = pg_query($link, $pg);
        if ($result == TRUE) {
          $pg = 'SELECT * FROM agencies WHERE agencyaddress = \'' . trim($_POST['address']) . '\';';
          $result = pg_query($link, $pg);
          $row = pg_fetch_array($result);
          $pg = "INSERT INTO users(username, password, userrank, agencyid) VALUES ('" . trim($_POST['username']) . "','" . $_POST['password'] . "', 0," . $row['agencyid'] . ");";
          $result = pg_query($link, $pg);
          if ($result == TRUE){
            session_start();
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['userrank'] = 0;
            $_SESSION['agencyid'] = $row['agencyid'];
            header('Location: index.php');
          }
        }
      }
  }
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Signup</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="entry-container">
      <form class="entry-form" action="signup.php" method="post">
        <p class="entry-title">SIGNUP</p>
        <label for="username">Username</label><br>
        <input type="text" name="username" required><br>
        <label for="password">Password</label><br>
        <input type="password" name="password" required><br>
        <label for="address">Address</label><br>
        <input type="text" name="address" required><br>
        <label for="phone">Phone</label><br>
        <input type="text" name="phone" required><br>
        <button class="btn-signup" name="btn-signup">Signup</button>
      </form>
      Have an existing account? <a href="login.php">Log in here</a>
    </div>
  </body>
</html>
