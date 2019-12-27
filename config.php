<?php
  define('DB_SERVER', 'ec2-107-21-122-38.compute-1.amazonaws.com');
  define('DB_USERNAME', 'xqztymjnwrxvbv');
  define('DB_PASSWORD', '81aa8ae9ff078765a949eb5bc7776497ff5e8decbb00cd418f35ab7a818a676a');
  define('DB_NAME', 'd6hpg0i5piiq40');

  /* Attempt to connect to PostgreSQL database */
  $link = pg_connect("host=".DB_SERVER." dbname=". DB_NAME ." user=" . DB_USERNAME . " password=" .DB_PASSWORD. "")
      or die('Could not connect: ' . pg_last_error());
 ?>
