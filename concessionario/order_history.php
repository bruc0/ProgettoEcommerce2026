<?php
require_once 'config.php';
if(currentUserId() === null) {
    header('Location: login.php');
    exit;
}
$userId = currentUserId();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

include 'header.php';
?>
<h2>I miei ordini</h2>
<?php if(isset($_GET['success'])) echo "<div class='alert alert-success'>Acquisto completato con successo!</div>"; ?>
<?php if(count($orders) > 0): ?>
    <?php foreach($orders as $order): ?>
        <div class="card mb-3">
            <div class="card-header">
                Ordine #<?= $order['id'] ?> del <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?> - Totale: <?= number_format((float) $order['total_amount'], 2, ',', '.') ?> €
            </div>
            <div class="card-body">
                <?php
                $stmtItems = $pdo->prepare("SELECT oi.*, a.marca, a.modello, a.immatricolazione FROM order_items oi JOIN autos a ON oi.car_id = a.id WHERE oi.order_id = ?");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll();
                ?>
                <ul>
                <?php foreach($items as $item): ?>
                    <li><?= htmlspecialchars($item['marca'] . ' ' . $item['modello']) ?> (<?= date('Y', strtotime($item['immatricolazione'])) ?>) - <?= number_format((float) $item['price_at_purchase'], 2, ',', '.') ?> €</li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Non hai ancora effettuato ordini. <a href="index.php">Acquista ora</a></p>
<?php endif; ?>
<?php include 'footer.php'; ?>
