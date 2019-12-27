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
    <title>ATN - Product List</title>
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
          <a href="#" class="active">Products</a>
          <a href="customers.php">Customers</a>
        </div>
      </div>
    </div>
    <main>
      <div class="header-bar">
        <button type="button" name="btn-new" class="btn-new" <?php if ($_SESSION['userrank'] < 1) echo ' style="display: none"'; ?>>New Product</button>
        <form class="search-form" action="index.html" method="post">
          <input type="text" name="product-search" placeholder="Search..."> by
          <select name="fields">
            <option value="productId">ID</option>
            <option value="productName">Name</option>
            <option value="productPrice">Price</option>
            <option value="supplierName">Supplier</option>
            <option value="categoryName">Category</option>
          </select>
        </form>
      </div>
      <div class="list-content">
        <?php
          require_once('config.php');
            $pg = "SELECT products.productid, products.productname, products.productprice, products.productinstock, suppliers.suppliername, categories.categoryname FROM products INNER JOIN suppliers ON products.supplierid = suppliers.supplierid INNER JOIN orderdetails ON products.productid = orderdetails.productid LEFT JOIN categories ON products.categoryid = categories.categoryid GROUP BY products.productid, suppliers.suppliername, categories.categoryname ORDER BY products.productid;";

          $result = pg_query($link, $pg);
          if (pg_num_rows($result) > 0) {
            echo '<table class = "tb-list">';
            echo '<thead>';
              echo '<tr>';
                echo '<th class = "tb-id">ID</th>';
                echo '<th class = "tb-pro">Name</th>';
                echo '<th class = "tb-tt">Price</th>';
                echo '<th class = "tb-id">In-stock</th>';
                echo '<th class = "tb-sup">Supplier</th>';
                echo '<th class = "tb-cat">Category</th>';
                echo '<th class = "tb-act">Action</th';
              echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = pg_fetch_array($result)){
              echo '<tr>';
                echo '<td class = "tb-id">' . $row['productid'] . '</td>';
                echo '<td class = "tb-pro">' . $row['productname'] . '</td>';
                echo '<td class = "tb-tt">' . $row['productprice'] . '</td>';
                echo '<td class = "tb-id">' . $row['productinstock'] . "</td>";
                echo '<td class = "tb-sup">' . $row['suppliername'] . '</td>';
                echo '<td class = "tb-cat">' . $row['categoryname'] . '</td>';
                echo '<td class = "tb-act">' . '' . '</td>';
              echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        else {
          echo 'There is no product. </br>';
        }
         ?>
      </div>
    </main>
  </body>
</html>
