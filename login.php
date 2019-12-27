<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])){
    require_once('config.php');
    $pg = "SELECT * FROM Users WHERE username = '".trim($_POST['username'])."' AND password = '".trim($_POST['password'])."'";
    $result = pg_query($link, $pg);

    if (pg_num_rows($result) == 1){
      $row = pg_fetch_array($result);
      session_start();
      $_SESSION['username'] = $row['username'];
      $_SESSION['userrank'] = $row['userrank'];
      $_SESSION['agencyid'] = $row['agencyid'];
      header('Location: index.php');
    }
    else {
       echo '<div class="login-error">Login failed. Username or password is invalid.</div>';
    }
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Login</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="entry-container">
      <form class="entry-form" action="login.php" method="post">
        <p class="entry-title">LOGIN</p>
        <label for="username">Username</label><br>
        <input type="text" name="username"><br>
        <label for="password">Password</label><br>
        <input type="password" name="password" value=""><br>
        <button class="btn-login" name="btn-login">Login</button>
      </form>
      Do not have an account? <a href="signup.php">Sign up here</a>
    </div>
  </body>
</html>
