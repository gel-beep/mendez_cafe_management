<?php
require_once "db.php";
function e($value) {
   return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
$editOrder = [
   "order_id" => "",
   "order_date" => date("Y-m-d"),
   "customer_name" => "",
   "total_amount" => "",
   "cafe_id" => ""
];
if (isset($_GET["edit"])) {
   $stmt = $pdo->prepare(
       "SELECT * FROM orders WHERE order_id = ?"
   );
   $stmt->execute([(int)$_GET["edit"]]);
   $foundOrder = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($foundOrder) {
       $editOrder = $foundOrder;
   }
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $action = $_POST["action"] ?? "";
   if ($action === "save") {
       $id = (int)($_POST["order_id"] ?? 0);
       $date = $_POST["order_date"] ?? "";
       $customer = trim($_POST["customer_name"] ?? "");
       $amount = $_POST["total_amount"] ?? "";
       $cafeId = (int)($_POST["cafe_id"] ?? 0);
       $validDate = DateTime::createFromFormat("!Y-m-d", $date);
       $validDate = $validDate && $validDate->format("Y-m-d") === $date;
       $validAmount = is_numeric($amount) && (float)$amount >= 0;
       $checkCafe = $pdo->prepare(
           "SELECT COUNT(*) FROM cafe WHERE cafe_id = ?"
       );
       $checkCafe->execute([$cafeId]);
       if (
           $validDate &&
           $customer !== "" &&
           $validAmount &&
           $checkCafe->fetchColumn() > 0
       ) {
           if ($id > 0) {
               $stmt = $pdo->prepare(
                   "UPDATE orders
                    SET order_date = ?, customer_name = ?,
                        total_amount = ?, cafe_id = ?
                    WHERE order_id = ?"
               );
               $stmt->execute([
                   $date, $customer, $amount, $cafeId, $id
               ]);
           } else {
               $stmt = $pdo->prepare(
                   "INSERT INTO orders
                    (order_date, customer_name, total_amount, cafe_id)
                    VALUES (?, ?, ?, ?)"
               );
               $stmt->execute([
                   $date, $customer, $amount, $cafeId
               ]);
           }
       }
       header("Location: orders.php");
       exit;
   }
   if ($action === "delete") {
       $id = (int)($_POST["order_id"] ?? 0);
       $stmt = $pdo->prepare(
           "DELETE FROM orders WHERE order_id = ?"
       );
       $stmt->execute([$id]);
       header("Location: orders.php");
       exit;
   }
}
$cafes = $pdo->query(
   "SELECT * FROM cafe ORDER BY cafe_name"
)->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query(
   "SELECT orders.*, cafe.cafe_name
    FROM orders
    INNER JOIN cafe ON orders.cafe_id = cafe.cafe_id
    ORDER BY orders.order_id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Orders</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
   <h1>Manage Orders</h1>
   <p>Child Entity</p>
</header>
<nav>
   <a href="index.php">Home</a>
   <a href="cafe.php">Manage Cafes</a>
   <a href="orders.php">Manage Orders</a>
</nav>
<main class="container">
   <h2><?= $editOrder["order_id"] ? "Update Order" : "Add an Order" ?></h2>
   <?php if (count($cafes) === 0): ?>
       <p>Please add a cafe first before creating an order.</p>
       <a class="button" href="cafe.php">Add a Cafe</a>
   <?php else: ?>
   <form method="POST" class="form-card">
       <input type="hidden" name="action" value="save">
       <input type="hidden" name="order_id"
              value="<?= e($editOrder["order_id"]) ?>">
       <label>Order Date</label>
       <input type="date" name="order_date"
              value="<?= e($editOrder["order_date"]) ?>" required>
       <label>Customer Name</label>
       <input type="text" name="customer_name"
              value="<?= e($editOrder["customer_name"]) ?>" required>
       <label>Total Amount (₱)</label>
       <input type="number" name="total_amount"
              min="0" step="0.01"
              value="<?= e($editOrder["total_amount"]) ?>" required>
       <label>Cafe</label>
       <select name="cafe_id" required>
           <option value="">Select a cafe</option>
           <?php foreach ($cafes as $cafe): ?>
               <option value="<?= e($cafe["cafe_id"]) ?>"
                   <?= (string)$editOrder["cafe_id"] ===
                       (string)$cafe["cafe_id"] ? "selected" : "" ?>>
                   <?= e($cafe["cafe_name"]) ?>
               </option>
           <?php endforeach; ?>
       </select>
       <button type="submit">
           <?= $editOrder["order_id"] ? "Update Order" : "Add Order" ?>
       </button>
       <?php if ($editOrder["order_id"]): ?>
           <a class="button secondary" href="orders.php">Cancel</a>
       <?php endif; ?>
   </form>
   <?php endif; ?>
   <h2>Order Records</h2>
   <div class="table-wrap">
       <table>
           <tr>
               <th>ID</th>
               <th>Date</th>
               <th>Customer</th>
               <th>Amount</th>
               <th>Cafe</th>
               <th>Actions</th>
           </tr>
           <?php foreach ($orders as $order): ?>
           <tr>
               <td><?= e($order["order_id"]) ?></td>
               <td><?= e($order["order_date"]) ?></td>
               <td><?= e($order["customer_name"]) ?></td>
               <td>₱<?= number_format((float)$order["total_amount"], 2) ?></td>
               <td><?= e($order["cafe_name"]) ?></td>
               <td class="actions">
                   <a class="button small"
                      href="orders.php?edit=<?= e($order["order_id"]) ?>">
                       Edit
                   </a>
                   <form method="POST"
                         onsubmit="return confirm('Delete this order?');">
                       <input type="hidden" name="action" value="delete">
                       <input type="hidden" name="order_id"
                              value="<?= e($order["order_id"]) ?>">
                       <button class="danger small" type="submit">
                           Delete
                       </button>
                   </form>
               </td>
           </tr>
           <?php endforeach; ?>
       </table>
   </div>
</main>
</body>
</html>
