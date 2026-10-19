<?php
require_once "db.php";
$cafeCount = $pdo->query(
   "SELECT COUNT(*) FROM cafe"
)->fetchColumn();
$orderCount = $pdo->query(
   "SELECT COUNT(*) FROM orders"
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cafe Management System</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
   <h1>Mendez Cafe</h1>
   <p>Cafe Management System</p>
</header>
<nav>
   <a href="index.php">Home</a>
   <a href="cafe.php">Manage Cafes</a>
   <a href="orders.php">Manage Orders</a>
</nav>
<main class="container">
   <h2>Welcome to Our Cafe!</h2>
   <p>Manage cafe details and customer orders in one place.</p>
   <div class="cards">
       <div class="card">
           <h3>Total Cafes</h3>
           <p class="number"><?= $cafeCount ?></p>
           <a class="button" href="cafe.php">View Cafes</a>
       </div>
       <div class="card">
           <h3>Total Orders</h3>
           <p class="number"><?= $orderCount ?></p>
           <a class="button" href="orders.php">View Orders</a>
       </div>
   </div>
   <h3>About the System</h3>
   <p>
       One cafe can have many orders. Each order belongs
       to one cafe through the cafe_id foreign key.
   </p>
</main>
<footer>
   <p>Cafe Management System | PDO Activity</p>
</footer>
</body>
</html>