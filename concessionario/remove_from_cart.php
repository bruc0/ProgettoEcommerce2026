<?php
require_once 'config.php';
if(currentUserId() === null) exit;
$car_id = $_GET['car_id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND car_id = ?");
$stmt->execute([currentUserId(), $car_id]);
header('Location: cart.php');
exit;
