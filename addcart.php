<?php
session_start();
if (isset($_GET['action'])){
  switch ($_GET['action']) {
    case 'add':
      if (isset($_GET['productid']) && $_GET['productid'] != ''){
        $isFound = FALSE;
        if (isset($_SESSION['cart'])){
          for ($i = 0; $i < count($_SESSION['cart']); ++$i){
            $item = $_SESSION['cart'][$i];
            if ($item['productid'] == $_GET['productid']){
              $_SESSION['cart'][$i]['quantity'] += $_GET['quantity'];
              $isFound = TRUE;
              break;
            }
          }
        }
        if (!isset($_SESSION['cart']) || !$isFound) {
          require_once('config.php');
          $pg = "SELECT products.productid, products.productname, products.productprice FROM products WHERE products.productid = " . $_GET['productid'] . ";";
          $result = pg_query($link, $pg);
          if (pg_num_rows($result) == 1){
            $row = pg_fetch_array($result);
            $num_row = count($_SESSION['cart']);
            $item = array(
              'productid' => $_GET['productid'],
              'productname' => $row['productname'],
              'productprice' => $row['productprice'],
              'quantity' => $_GET['quantity']
            );
            $_SESSION['cart'][$num_row] = $item;
          }
          else {
              echo "The item does not exist";
          }
        }
      }
      break;
    case 'remove':
      if (isset($_GET['productid']) && $_GET['productid'] != ''){
        if (!empty($_SESSION['cart'])){
          foreach ($_SESSION['cart'] as $i => $item){
            if ($item['productid'] = $_GET['productid']){
              unset($_SESSION['cart'][$i]);
              break;
            }
          }
        }
      }
      break;
    default:
      // code...
      break;
  }
}

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0){
  $total = 0;
?>
<table>
  <thead>
    <th>#</th>
    <th>Name</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Action</th>
  </thead>
  <tbody>
    <?php
      foreach ($_SESSION['cart'] as $key => $value){
        $item = $_SESSION['cart'][$key];
        $subtotal = preg_replace("/([^0-9\\.])/i", "", $item['productprice']) * $item['quantity'];
        $total += $subtotal;
        echo '<tr>';
        echo '<td>' . $key . '</td>';
        echo '<td>' . $item['productname'] . '</td>';
        echo '<td>' . $item['productprice'] . '</td>';
        echo '<td>' . $item['quantity'] . '</td>';
        echo '<td>' . $subtotal . '</td>';
        echo '<td><a href="#" onclick="cartAction(\'remove\', ' . $item['productid'] . ')"">Remove</a> </td>';
        echo '</tr>';
      }
      echo '<tr>';
      echo '<td colspan="4" style="text-align: center">Total</td>';
      echo '<td>' . $total . '</td>';
      echo '</tr>';
      ?>
  </tbody>
</table>
<?php } ?>
