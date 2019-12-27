<?php
  session_start();
  if (!isset($_SESSION["username"]) || $_SESSION["username"] == '')
  {
          header("Location: login.php");
  }
  require_once('config.php');
  $thisyear = date('Y');
  $month = date('m');
  if ($_SESSION['userrank'] < 1){
    $pg = "SELECT EXTRACT(MONTH FROM orders.orderDate) AS Month, SUM(orderdetails.quantity * products.productPrice) AS Total FROM orders INNER JOIN orderdetails ON orders.orderId = orderdetails.orderId, products WHERE EXTRACT(YEAR FROM orders.orderdate) = " . $thisyear . " AND orders.agencyid = " . $_SESSION['agencyid'] . " AND orderdetails.productId = products.productId GROUP BY EXTRACT(MONTH FROM orders.orderDate);";
    $bs = "SELECT EXTRACT(MONTH FROM orders.orderdate) AS month, agencies.agencyaddress, products.productname, SUM(orderdetails.quantity) AS squantity FROM orders INNER JOIN agencies ON orders.agencyid = agencies.agencyid INNER JOIN orderdetails ON orders.orderid = orderdetails.orderid INNER JOIN products ON products.productid = orderdetails.productid WHERE agencies.agencyid = " . $_SESSION['agencyid'] . " AND EXTRACT(YEAR FROM orders.orderdate) = " . $thisyear . " AND EXTRACT(MONTH FROM orders.orderdate) = " . $month . " GROUP BY month, agencies.agencyaddress, products.productname
ORDER BY squantity DESC;";
    $hs = "SELECT products.productname, products.productinstock FROM products ORDER BY products.productinstock DESC;";
    $opt = "SELECT customers.customername AS first, COUNT(*) AS third, SUM(orderdetails.quantity*products.productprice) AS second FROM customers INNER JOIN orders ON customers.customerid = orders.customerid INNER JOIN orderdetails ON orders.orderid = orderdetails.orderid INNER JOIN products ON products.productid = orderdetails.productid WHERE EXTRACT(YEAR FROM orders.orderdate) = " . $thisyear . " AND EXTRACT(MONTH FROM orders.orderdate) = " . $month . " AND orders.agencyid = " . $_SESSION['agencyid'] . " GROUP BY customers.customername, EXTRACT(YEAR FROM orders.orderdate), EXTRACT(MONTH FROM orders.orderdate) ORDER BY second DESC, third DESC;";
  }
  else {
    $pg = "SELECT EXTRACT(MONTH FROM orders.orderDate) AS Month, SUM(orderdetails.quantity * products.productPrice) AS Total FROM orders INNER JOIN orderdetails ON orders.orderId = orderdetails.orderId, products WHERE EXTRACT(YEAR FROM orders.orderdate) = " . $thisyear . " AND orderdetails.productId = products.productId GROUP BY EXTRACT(MONTH FROM orders.orderDate);";
    $bs = "SELECT EXTRACT(MONTH FROM orders.orderdate) AS month, products.productname, SUM(orderdetails.quantity) AS squantity FROM orders INNER JOIN orderdetails ON orders.orderid = orderdetails.orderid INNER JOIN products ON products.productid = orderdetails.productid WHERE EXTRACT(YEAR FROM orders.orderdate) = " . $thisyear . " AND EXTRACT(MONTH FROM orders.orderdate) = " . $month . "  GROUP BY month, products.productname ORDER BY squantity DESC;";
    $hs = "SELECT products.productname, products.productinstock FROM products ORDER BY products.productinstock DESC;";
    $opt = "SELECT EXTRACT(MONTH FROM orders.orderdate) AS month, agencies.agencyaddress AS first, SUM(orderdetails.quantity * products.productPrice) AS second FROM agencies INNER JOIN orders ON agencies.agencyid = orders.agencyid INNER JOIN orderdetails ON orders.orderid = orderdetails.orderid INNER JOIN products ON products.productid = orderdetails.productid GROUP BY EXTRACT(YEAR FROM orders.orderdate), EXTRACT(MONTH FROM orders.orderdate), agencies.agencyaddress ORDER BY second DESC;";
  }
  //Revenue
  $result = pg_query($link, $pg);
  $month = array();
  $last_month = 0;
  $this_month = 0;
  if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_array($result)){
      $month[$row['month']] = $row['total'];
      if ($row['month'] > $this_month) {
          $last_month = $this_month;
          $this_month = $row['month'];
      }
    }
    //Trend
    if ($last_month == 0) $trend = 999999.00;
    else {
      $this_mon = preg_replace("/([^0-9\\.])/i", "", $month[$this_month]);
      $last_mon = preg_replace("/([^0-9\\.])/i", "", $month[$last_month]);
      $trend = (($this_mon/$last_mon) - 1.0) * 100.0;
    }
  }
  else {
    $month[$this_month] = '$0.00';
    $trend = 999999.00;
  }
  //Best selling product
  $result = pg_query($link, $bs);
  if (pg_num_rows($result) > 0) {
    $row = pg_fetch_array($result);
    $bs_product = $row['productname'];
    $bs_quantity = $row['squantity'];
  }
  else {
    $bs_product = 'NaN';
  }
  //Highes in-stock product
  $result = pg_query($link, $hs);
  if (pg_num_rows($result) > 0) {
    $row = pg_fetch_array($result);
    $hs_product = $row['productname'];
    $hs_quantity = $row['productinstock'];
  }
  else {
    $hs_product = 'NaN';
  }
  //Optional
  $result = pg_query($link, $opt);
  if (pg_num_rows($result) > 0) {
    $row = pg_fetch_array($result);
    $opt_first = $row['first'];
    $opt_second = $row['second'];
    if (isset($row['third'])) $opt_third = $row['third'];
  }
  else {
    $opt_first = 'NaN';
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script>
      window.onload = function () {

      var chart = new CanvasJS.Chart("chartContainer", {
      	animationEnabled: true,
      	theme: "light2", // "light1", "light2", "dark1", "dark2"
      	title:{
      		text: <?php if ($_SESSION['userrank'] > 0) echo '"Revenue of the company each month over the year"';
                      else echo '"Revenue of the shop each month over the time"';?>
      	},
      	axisY: {
      		title: "Revenue (dollar)"
      	},
        axisX: {
          title: "Month"
        },
      	data: [{
      		type: "column",
      		showInLegend: true,
      		legendMarkerColor: "grey",
      		legendText: "Dollars",
      		dataPoints: [
            <?php
              for ($i = 1; $i <= 12; ++$i){
                if (isset($month[$i])) echo '{ y: ' . preg_replace("/([^0-9\\.])/i", "", $month[$i]) . ' , label: ' . $i . ' },';
                else echo '{ y: 0, label: ' . $i . ' },';
              }
            ?>
      		]
      	}]
      });
      chart.render();

      }
    </script>
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
          <a href="#" class="active">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="products.php">Products</a>
          <a href="customers.php">Customers</a>
        </div>
      </div>
    </div>
    <main>
      Revenue<br>
      This month: <?php echo $month[$this_month] ?><br>
      Trend: <?php
                echo '<p class = "txt-trend ';
                if ($trend == 999999.00) echo '"> NaN';
                else
                if ($trend >= 0) echo 'txt-positive"> ↑' . number_format(abs($trend), 1) . '%';
                   else echo 'txt-negative"> ↓' . number_format(abs($trend), 1) . '%';
                echo '</p>'; ?><br>
      Best selling product: <?php echo $bs_product; if ($bs_product != 'NaN') echo " (" . $bs_quantity . " qty)" ?><br>
      Highest stock product: <?php echo $hs_product; if ($hs_product != 'NaN') echo " (" . $hs_quantity . " qty)" ?><br>
      <?php if ($_SESSION['userrank'] < 1) {
        echo 'Best customer: ' . $opt_first;
        if ($opt_first != 'NaN') echo ' (' . $opt_second . ' | ' . $opt_third . ' order(s)) <br>';
      }
      else {
        echo 'Best agency: ' . $opt_first;
        if($opt_first != 'NaN') echo ' (' . $opt_second . ') <br>';
      } ?>
      <div id="chartContainer" style="height: 300px; width: 100%;"></div>
    </main>
  </body>
</html>
