<?php
  session_start();
  if (!isset($_SESSION["username"]) || $_SESSION["username"] == '')
  {
          header("Location: login.php");
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Customer List</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="navbar">
      <div class="nav-container">
        <div class="navbar-info">
          <div class="info-container">
            <?php echo '<a href="account.php">' . $_SESSION['username'] . '</a>'; ?>
          </div>
        </div>
        <div class="navbar-menu">
          <a href="index.php">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="products.php">Products</a>
          <a href="#" class="active">Customers</a>
        </div>
      </div>
    </div>
    <main>
      <div class="header-bar">
        <button type="button" name="btn-new" class="btn-new"<?php if ($_SESSION['userrank'] > 0) echo ' style="display: none"'; ?>>New Customer</button>
        <form class="search-form" action="index.html" method="post">
          <input type="text" name="customer-search" placeholder="Search..."> by
          <select name="fields">
            <option value="customerId">ID</option>
            <option value="customerName">Name</option>
            <option value="customerAddress">Address</option>
            <option value="customerPhone">Phone</option>
          </select>
        </form>
      </div>
      <div class="list-content">
        <?php
          require_once('config.php');
          $pg = "SELECT * FROM customers";
          $result = pg_query($link, $pg);
          if (pg_num_rows($result) > 0) {
            echo '<table class = "tb-list">';
            echo '<thead>';
              echo '<tr>';
                echo '<th class = "tb-id">ID</th>';
                echo '<th class = "tb-cus">Name</th>';
                echo '<th class = "tb-gen">Gender</th>';
                echo '<th class = "tb-date">Birthday</th>';
                echo '<th class = "tb-pho">Phone</th>';
                echo '<th class = "tb-addr">Address</th>';
                echo '<th class = "tb-email">Email</th>';
                echo '<th class = "tb-act">Action</th';
              echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = pg_fetch_array($result)){
              echo '<tr>';
                echo '<td class = "tb-id">' . $row['customerid'] . '</td>';
                echo '<td class = "tb-cus">' . $row['customername'] . '</td>';
                echo '<td class = "tb-gen">';
                if ($row['customergender'] == 0) echo '<input type="radio" checked disabled>';
                else echo '<input type="radio" disabled>';
                echo '</td>';
                echo '<td class = "tb-date">' . $row['customerdob'] . "</td>";
                echo '<td class = "tb-pho">' . $row['customerphone'] . "</td>";
                echo '<td class = "tb-addr">' . $row['customeraddress'] . '</td>';
                echo '<td class = "tb-email">' . $row['customeremail'] . '</td>';
                echo '<td class = "tb-act">' . '' . '</td>';
              echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        else {
          echo 'There is no customer. </br>';
        }
         ?>
      </div>
    </main>
  </body>
</html>
