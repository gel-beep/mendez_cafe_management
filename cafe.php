<?php
require_once "db.php";
function e($value) {
   return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
$editCafe = [
   "cafe_id" => "",
   "cafe_name" => "",
   "address" => ""
];
if (isset($_GET["edit"])) {
   $stmt = $pdo->prepare(
       "SELECT * FROM cafe WHERE cafe_id = ?"
   );
   $stmt->execute([(int)$_GET["edit"]]);
   $foundCafe = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($foundCafe) {
       $editCafe = $foundCafe;
   }
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $action = $_POST["action"] ?? "";
   if ($action === "save") {
       $id = (int)($_POST["cafe_id"] ?? 0);
       $name = trim($_POST["cafe_name"] ?? "");
       $address = trim($_POST["address"] ?? "");
       if ($name !== "") {
           if ($id > 0) {
               $stmt = $pdo->prepare(
                   "UPDATE cafe
                    SET cafe_name = ?, address = ?
                    WHERE cafe_id = ?"
               );
               $stmt->execute([$name, $address, $id]);
           } else {
               $stmt = $pdo->prepare(
                   "INSERT INTO cafe (cafe_name, address)
                    VALUES (?, ?)"
               );
               $stmt->execute([$name, $address]);
           }
       }
       header("Location: cafe.php");
       exit;
   }
   if ($action === "delete") {
       $id = (int)($_POST["cafe_id"] ?? 0);
       $stmt = $pdo->prepare(
           "DELETE FROM cafe WHERE cafe_id = ?"
       );
       $stmt->execute([$id]);
       header("Location: cafe.php");
       exit;
   }
}
$cafes = $pdo->query(
   "SELECT * FROM cafe ORDER BY cafe_id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Cafes</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
   <h1>Manage Cafes</h1>
   <p>Parent Entity</p>
</header>
<nav>
   <a href="index.php">Home</a>
   <a href="cafe.php">Manage Cafes</a>
   <a href="orders.php">Manage Orders</a>
</nav>
<main class="container">
   <h2><?= $editCafe["cafe_id"] ? "Update Cafe" : "Add a Cafe" ?></h2>
   <form method="POST" class="form-card">
       <input type="hidden" name="action" value="save">
       <input type="hidden" name="cafe_id"
              value="<?= e($editCafe["cafe_id"]) ?>">
       <label>Cafe Name</label>
       <input type="text" name="cafe_name"
              value="<?= e($editCafe["cafe_name"]) ?>"
              required>
       <label>Address</label>
       <input type="text" name="address"
              value="<?= e($editCafe["address"]) ?>">
       <button type="submit">
           <?= $editCafe["cafe_id"] ? "Update Cafe" : "Add Cafe" ?>
       </button>
       <?php if ($editCafe["cafe_id"]): ?>
           <a class="button secondary" href="cafe.php">Cancel</a>
       <?php endif; ?>
   </form>
   <h2>Cafe Records</h2>
   <div class="table-wrap">
       <table>
           <tr>
               <th>ID</th>
               <th>Cafe Name</th>
               <th>Address</th>
               <th>Actions</th>
           </tr>
           <?php foreach ($cafes as $cafe): ?>
           <tr>
               <td><?= e($cafe["cafe_id"]) ?></td>
               <td><?= e($cafe["cafe_name"]) ?></td>
               <td><?= e($cafe["address"]) ?></td>
               <td class="actions">
                   <a class="button small"
                      href="cafe.php?edit=<?= e($cafe["cafe_id"]) ?>">
                       Edit
                   </a>
                   <form method="POST"
                         onsubmit="return confirm('Delete this cafe and its orders?');">
                       <input type="hidden" name="action" value="delete">
                       <input type="hidden" name="cafe_id"
                              value="<?= e($cafe["cafe_id"]) ?>">
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