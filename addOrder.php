<?php
  session_start();
  require_once('config.php');
  $pg = 'SELECT customers.customerid FROM customers WHERE customers.customerphone=\'' . $_POST['customerphone'] . '\';';
  $result = pg_query($link, $pg);
  if (pg_num_rows($result) == 1){
    $row = pg_fetch_array($result);
    $customerid = $row['customerid'];
  }

  $pg = 'INSERT INTO orders(agencyid, customerid, orderdate, shippingaddress) VALUES';
  $pg .= '(' . $_SESSION['agencyid'] . ',';
  $pg .= $customerid . ',\''. $_POST['orderdate'] . "','" . $_POST['shippingaddress'] . "');";
  $result = pg_query($link, $pg);

  $pg = "SELECT MAX(orders.orderid) FROM orders WHERE orders.agencyid = " . $_SESSION['agencyid'] . ";";
  $result = pg_query($link, $pg);
  if (pg_num_rows($result)){
    $row = pg_fetch_array($result);
    $orderid = $row['orderid'];
  }

  $pg = "INSERT INTO orderdetails(orderid, productid, quantity)";
  foreach ($_SESSION['cart'] as $key => $item){
     $pg .= " VALUES(" . $orderid . "," . $item['productid'] . "," . $item['quantity'] . ")";
     if ($item == end($_SESSION['cart'])) {
        $pg .= ";";
     }
     else {
       $pg .= ",";
     }
  }
  $result = pg_query($link, $pg);
  unset($_SESSION['cart']);
  $result = TRUE;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - New Order</title>
  </head>
  <body>
    <?php if ($result == TRUE) { ?>
      The order is created succesfully. Do you want to <a href="index.php">go back to the dashboard</a> or <a href="new_order.php">create another new order</a>.
    <?php } else { ?>
      The order is not created. Please <a href="new_order.php">go back</a>.
    <?php } ?>
  </body>
</html>
