<?php
  session_start();
  if (!isset($_SESSION["username"]) || $_SESSION["username"] == '')
  {
          header("Location: login.php");
  }
  require_once('config.php');
  if (!isset($agencyaddress)){
    $pg = "SELECT agencies.agencyaddress FROM agencies WHERE agencies.agencyid = " . $_SESSION['agencyid'] . ";";
    $result = pg_query($link, $pg);
    if (pg_num_rows($result) == 1){
      $row = pg_fetch_array($result);
      $agencyaddress = $row['agencyaddress'];
    }
  }

  $pg = "SELECT customers.customerphone FROM customers";
  $result = pg_query($link, $pg);
  while ($row = pg_fetch_array($result)){
    $customerlist[] = $row;
  }



  $pg = "SELECT products.productname FROM products ORDER BY products.productid";
  $result = pg_query($link, $pg);
  while ($row = pg_fetch_array($result)){
    $productlist[] = $row;
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ATN - New Order</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script type="text/javascript">
        function showName(str) {
          var xhttp;
          if (str.length == 0) {
            document.getElementById("customername").value = "";
            return;
          }
          xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("customername").value = this.responseText;
            }
          };
          xhttp.open("GET", "getname.php?q="+str, true);
          xhttp.send();
        }

        function getId(str) {
          var xhttp;
          if (str.length == 0) {
            document.getElementById("productid").value = "";
            return;
          }
          xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("productid").value = this.responseText;
            }
          };
          xhttp.open("GET", "getid.php?q="+str, true);
          xhttp.send();
        }

        function cartAction(act, itemid){
          var xhttp;
          var querystring;
          xhttp = new XMLHttpRequest();
          var querystring = "addcart.php?action=";
          switch (act) {
            case 'add':
              querystring += act + "&productid=" + document.getElementById("productid").value + "&quantity=" + document.getElementById("quantity").value;
              document.getElementById('productname').value = "";
              document.getElementById('quantity').value = "";
              break;
            case 'remove':
              querystring += act + "&productid=" + itemid;
              break;
            default:

              break;
          }

          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("cart-list").innerHTML = this.responseText;
            }
          };

          xhttp.open("GET", querystring, true);
          xhttp.send();
        }
    </script>
  </head>
  <body>
    <div class="header-bar single-bar">
      <a href="orders.php"><button type="button" name="btn-back" class="btn-back">Back</button></a>
      <div class="single-title">New Order</div>
    </div>
    <div class="single-content">
      <form class="" action="addOrder.php" method="post">
        <div class="order-info">
          <div class="single-label">Information</div>
          <div class="single-section-content">
            <table class="order-info-tb">
              <tbody>
                <tr>
                  <td class="order-info-lb">Agency</td>
                  <td class="order-info-dt"> <input type="text" name="agencyname" value="<?php echo $agencyaddress; ?>" disabled></td>
                  <td class="order-info-lb">Order Date</td>
                  <td class="order-info-dt"><input type="text" name="orderdate" value="<?php echo date('Y-m-d');?>" disabled></td>
                </tr>
                <tr>
                  <td class="order-info-lb">Customer phone</td>
                  <td class="order-info-dt"> <input list="customerphone" name="customerphone" value="" onkeyup="showName(this.value)"> <datalist id="customerphone" class="">
                    <?php foreach ($customerlist as $customer){
                      echo '<option value = "' . $customer['customerphone'] . '">';
                    } ?>
                  </datalist> </td>
                  <td class="order-info-lb">Customer name</td>
                  <td class="order-info-dt"> <input id="customername" type="text" name="" value="" disabled> </td>
                  <div id="customerinfo" style="display: none"></div>
                </tr>
                <tr>
                  <td class="order-info-lb">Shipping address</td>
                  <td colspan="3" class="order-info-dt"><input type="text" name="shippingaddress" value=""> </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="product-list">
          <div class="single-label">Product list</div>
          <div class="single-section-content">
            Add a product: <input list="productlist" id="productname" name="" value="" onkeyup="getId(this.value)">
            <input type="hidden" id="productid" name="productid" value="">
            <datalist id="productlist">
                <?php foreach ($productlist as $product) {
                  echo '<option value = "' . $product['productname'] . '">';
                } ?>
            </datalist>
            Quantity: <input type="number" name="quantity" min="1" max="9999" id="quantity" value="1">
            <button type="button" name="" onclick="cartAction('add',0)">Add</button>
            <div id="cart-list"></div>
            <script type="text/javascript">
              if (document.readyState == 4 && document.status == 200) {
                cartAction("");
              }
            </script>
          </div>
        </div>
        <div class="form-button-bar">
          <input type="submit" class="btn-add" name="btn-add" value="Save">
        </div>
      </form>
    </div>
  </body>
</html>
