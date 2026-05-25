<?php
require_once 'config.php';
if(currentUserId() === null) {
    header('Location: login.php');
    exit;
}
$userId = currentUserId();

$stmt = $pdo->prepare("SELECT car_id FROM cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartCars = $stmt->fetchAll(PDO::FETCH_COLUMN);
if(empty($cartCars)) {
    header('Location: cart.php');
    exit;
}

$placeholders = implode(',', array_fill(0, count($cartCars), '?'));
$stmt = $pdo->prepare("SELECT SUM(prezzo) as total FROM autos WHERE id IN ($placeholders) AND NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.car_id = autos.id)");
$stmt->execute($cartCars);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$userId, $total]);
    $orderId = $pdo->lastInsertId();

    foreach($cartCars as $carId) {
        $stmtPrice = $pdo->prepare("SELECT prezzo FROM autos WHERE id = ?");
        $stmtPrice->execute([$carId]);
        $price = $stmtPrice->fetchColumn();
        if ($price === false) {
            continue;
        }

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, car_id, price_at_purchase) VALUES (?, ?, ?)");
        $stmtItem->execute([$orderId, $carId, $price]);
    }

    $stmtDel = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmtDel->execute([$userId]);

    $pdo->commit();
    header('Location: order_history.php?success=1');
} catch(Exception $e) {
    $pdo->rollBack();
    die("Errore durante l'acquisto: " . $e->getMessage());
}
exit;
