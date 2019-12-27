<?php
  require_once('config.php');
  $pg = "SELECT products.productid, products.productname, products.productprice FROM products ORDER BY products.productid";
  $result = pg_query($link, $pg);
  if (pg_num_rows($result)){
    while ($row=pg_fetch_array($result)) {
        $id[$row['productname']] = $row['productid'];
    }
  }
  $q = $_REQUEST["q"];
  $proid = "";

  if ($q !== ""){
    if (isset($id[$q]) || $id[$q] != "") $proid = $id[$q];
    else $proid = "";
  }

  echo $proid;
?>
