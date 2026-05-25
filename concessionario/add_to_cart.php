<?php
require_once 'config.php';
if(currentUserId() === null) {
    header('Location: login.php');
    exit;
}
$car_id = $_GET['car_id'] ?? 0;
$stmt = $pdo->prepare("SELECT id FROM autos WHERE id = ? AND NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.car_id = autos.id)");
$stmt->execute([$car_id]);
if($stmt->rowCount() > 0) {
    $stmt2 = $pdo->prepare("INSERT IGNORE INTO cart (user_id, car_id) VALUES (?, ?)");
    $stmt2->execute([currentUserId(), $car_id]);
}
header('Location: cart.php');
exit;
