<?php
  require_once('config.php');
  if (!isset($name)){
    $pg = "SELECT customers.customerid, customers.customerphone, customers.customername FROM customers";
    $result = pg_query($link, $pg);
    if (pg_num_rows($result)){
      while ($row=pg_fetch_array($result)) {
          $name[$row['customerphone']] = $row['customername'];
      }
    }
  }
  $q = $_REQUEST["q"];
  $cusname = "";

  if ($q !== ""){
    if (isset($name[$q]) || $name[$q] != "") $cusname = $name[$q];
    else $cusname = "";
  }

  echo $cusname;
?>
