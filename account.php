<?php
  session_start();
  if (isset($_POST['logout'])){
    session_unset();
    session_destroy();
  }
  if (!isset($_SESSION["username"]) || $_SESSION["username"] == '')
  {
          header("Location: login.php");
  }
  require_once('config.php');
  $pg = "SELECT users.username, users.userrank, agencies.agencyaddress, agencies.agencyphone FROM users LEFT JOIN agencies ON users.agencyid = agencies.agencyid WHERE users.username = '" . $_SESSION['username'] . "';";
  $result = pg_query($link, $pg);
  if (pg_num_rows($result) == 1){
    $row = pg_fetch_array($result);
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Product List</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="navbar">
      <div class="nav-container">
        <div class="navbar-info">
          <div class="info-container">
            <a href="#" class="active"><?php echo $_SESSION['username']; ?></a>
          </div>
        </div>
        <div class="navbar-menu">
          <a href="index.php">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="products.php">Products</a>
          <a href="customers.php">Customers</a>
        </div>
      </div>
    </div>
    <main>
      Your info <br>
      --------- <br>
      Username: <?php echo $row['username']; ?><br>
      Role: <?php switch ($row['userrank']) {
        case 0:
          echo "Agency<br>";
          echo "Your agency address: " . $row['agencyaddress'] . "<br>";
          echo "Your agency phone: " . $row['agencyphone'] . "<br>";
          break;
        case 1:
          echo "Director";
          break;
        default:
          // code...
          break;
      } ?> <br>
      Reset password <br>
      --------- <br>
      <form class="" action="account.php" method="post">
          <label for="">Enter new password: </label>
          <input type="password" name="" value=""> <br>
          <label for="">Re-enter new passworrd: </label>
          <input type="password" name="" value=""> <br>
          <input type="submit" name="" value="Reset">
      </form> <br>
      <form class="" action="account.php" method="post">
        <input type="submit" name="logout" value="Logout">
      </form>
    </main>
  </body>
</html>
