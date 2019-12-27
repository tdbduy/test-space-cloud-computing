<?php
  session_start();
  if (!isset($_SESSION["username"]) || $_SESSION["username"] == '')
  {
          header("Location: login.php");
  }
  require_once('config.php');
  $pg = "SELECT agencies.agencyaddress, orders.orderdate, customers.customername, customers.customerphone, orders.shippingaddress FROM orders INNER JOIN agencies ON orders.agencyid = agencies.agencyid INNER JOIN customers ON orders.customerid = customers.customerid WHERE orders.orderid = " . $_GET['id'] . ";";
  $result = pg_query($link, $pg);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - Order #<?php echo $_GET['id']; ?></title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <?php if (pg_num_rows($result) > 0) {
      $row = pg_fetch_array($result);?>
    <div class="header-bar single-bar">
      <a href="orders.php"><button type="button" name="btn-back" class="btn-back">Back</button></a>
      <div class="single-title">Order #<?php echo $_GET['id']; ?></div>
    </div>
    <div class="single-content">
      <div class="order-info">
        <div class="single-label">Information</div>
        <div class="single-section-content">
          <table class="order-info-tb">
            <tbody>
              <tr>
                <td class="order-info-lb">Agency</td>
                <td class="order-info-dt"><?php echo $row['agencyaddress']; ?></td>
                <td class="order-info-lb">Order Date</td>
                <td class="order-info-dt"><?php echo $row['orderdate']; ?></td>
              </tr>
              <tr>
                <td class="order-info-lb">Customer phone</td>
                <td class="order-info-dt"><?php echo $row['customerphone']; ?></td>
                <td class="order-info-lb">Customer name</td>
                <td class="order-info-dt"><?php echo $row['customername']; ?></td>
              </tr>
              <tr>
                <td class="order-info-lb">Shipping address</td>
                <td colspan="3" class="order-info-dt"><?php echo $row['shippingaddress']; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="product-list">
        <div class="single-label">Product list</div>
        <div class="single-section-content">
          <?php
            $pg = "SELECT products.productname, products.productprice, orderdetails.quantity, SUM(orderdetails.quantity * products.productprice) AS subtotal FROM orderdetails INNER JOIN products ON orderdetails.productid = products.productid WHERE orderdetails.orderid = " . $_GET['id'] . " GROUP BY products.productname, products.productprice, orderdetails.quantity;";
            $result = pg_query($pg);
            if (pg_num_rows($result) > 0) {
              echo '<table>';
              echo '<thead>';
              echo '<th>Product name</th>';
              echo '<th class="tb-tt">Price</th>';
              echo '<th class="tb-tt">Quantity</th>';
              echo '<th class="tb-tt">Subtotal</th>';
              echo '</thead>';
              echo '<tbody>';
              $total = 0;
              while ($row = pg_fetch_array($result)){
                echo '<tr>';
                echo '<td>' . $row['productname'] . '</td>';
                echo '<td class="tb-tt">' . $row['productprice'] . '</td>';
                echo '<td class="tb-tt">' . $row['quantity'] . '</td>';
                echo '<td class="tb-tt">' . $row['subtotal'] . '</td>';
                echo '</tr>';
                $total = $total + preg_replace("/([^0-9\\.])/i", "", $row['subtotal']);
              };
              echo '<tr><td colspan="3" style="text-align: center">Total</td><td>$'.$total.'</td></tr>';
              echo '</tbody>';
              echo '</table>';
            }
            else {
              echo 'No product in this order.<br>';
            }
           ?>

        </div>
      </div>
    <?php }
      else { echo "The order does not exist. Please <a href=\"orders.php\">Go back</a>."; } ?>
    </div>
  </body>
</html>
