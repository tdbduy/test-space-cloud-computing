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
    <title>ATN - Order List</title>
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
          <a href="#" class="active">Orders</a>
          <a href="products.php">Products</a>
          <a href="customers.php">Customers</a>
        </div>
      </div>
    </div>
    <main>
      <div class="header-bar">
        <a href="new_order.php"><button type="button" name="btn-new" class="btn-new"<?php if ($_SESSION['userrank'] > 0) echo ' style="display: none"'; ?>>New Order</button></a> 
        <form class="search-form" action="index.html" method="post">
          <input type="text" name="customer-search" placeholder="Search..."> by
          <select name="fields">
            <option value="orderId">ID</option>
            <option value="orderDate">Date</option>
            <option value="agencyName">Agency</option>
            <option value="customerName">Customer</option>
          </select>
        </form>
      </div>
      <div class="list-content">
        <?php
          require_once('config.php');
          if ($_SESSION['userrank'] < 1) {
            $pg = "SELECT Orders.orderid, Orders.orderdate, Agencies.agencyAddress, Customers.customerName, SUM(OrderDetails.quantity * Products.productPrice) AS total FROM (((OrderDetails INNER JOIN Orders ON OrderDetails.orderId = Orders.orderId) INNER JOIN Customers ON Customers.customerId = Orders.customerId) INNER JOIN Products ON Products.productId = OrderDetails.productId) INNER JOIN Agencies ON Agencies.agencyId = Orders.agencyId WHERE Orders.agencyId = " . $_SESSION['agencyid'] . " GROUP BY Orders.orderId, Customers.customerName, Agencies.agencyAddress ORDER BY Orders.orderDate DESC, orders.orderid DESC;";
          }
          else {
            $pg = "SELECT Orders.orderid, Orders.orderdate, Agencies.agencyAddress, Customers.customerName, SUM(OrderDetails.quantity * Products.productPrice) AS total FROM (((OrderDetails INNER JOIN Orders ON OrderDetails.orderId = Orders.orderId) INNER JOIN Customers ON Customers.customerId = Orders.customerId) INNER JOIN Products ON Products.productId = OrderDetails.productId) INNER JOIN Agencies ON Agencies.agencyId = Orders.agencyId GROUP BY Orders.orderId, Customers.customerName, Agencies.agencyAddress ORDER BY Orders.orderDate DESC, orders.orderid DESC;";
          };
          $result = pg_query($link, $pg);
          if (pg_num_rows($result) > 0) {
            echo '<table class = "tb-list">';
            echo '<thead>';
              echo '<tr>';
                echo '<th class = "tb-id">ID</th>';
                echo '<th class = "tb-date">Order Date</th>';
                if ($_SESSION['userrank'] > 0) echo '<th class = "tb-cus">Agency</th>';
                echo '<th class = "tb-cus">Customer</th>';
                echo '<th class = "tb-tt">Total</th>';
                echo '<th class = "tb-act">Action</th';
              echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = pg_fetch_array($result)){
              echo '<tr>';
                echo '<td class = "tb-id">' . $row['orderid'] . '</td>';
                echo '<td class = "tb-date">' . $row['orderdate'] . '</td>';
                if ($_SESSION['userrank'] > 0) echo '<td clas = "tb-agen">' . $row['agencyaddress'] . '</td>';
                echo '<td class = "tb-cus">' . $row['customername'] . "</td>";
                echo '<td class = "tb-tt">' . $row['total'] . '</td>';
                echo '<td class = "tb-act"><a href="single_order.php?id=' . $row['orderid'] . '">Detail</a></td>';
              echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
          }
          else {
            echo 'There is no order. </br>';
          }
         ?>
      </div>
    </main>
  </body>
</html>
