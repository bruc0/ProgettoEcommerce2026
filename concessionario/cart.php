<?php
require_once 'config.php';
if(currentUserId() === null) {
    header('Location: login.php');
    exit;
}
$userId = currentUserId();
$stmt = $pdo->prepare("SELECT c.car_id, autos.* FROM cart c JOIN autos ON c.car_id = autos.id WHERE c.user_id = ? AND NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.car_id = autos.id)");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();
$total = array_sum(array_column($cartItems, 'prezzo'));

include 'header.php';
?>
<h2>Il mio carrello</h2>
<?php if(count($cartItems) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>Auto</th><th>Prezzo</th><th>Azione</th></tr>
            </thead>
            <tbody>
                <?php foreach($cartItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['marca'] . ' ' . $item['modello'] . ' (' . date('Y', strtotime($item['immatricolazione'])) . ')') ?></td>
                    <td><?= number_format((float) $item['prezzo'], 2, ',', '.') ?> €</td>
                    <td><a href="remove_from_cart.php?car_id=<?= $item['car_id'] ?>" class="btn btn-danger btn-sm">Rimuovi</a></td>
                </tr>
                <?php endforeach; ?>
                <tr class="fw-bold"><td>Totale</td><td colspan="2"><?= number_format((float) $total, 2, ',', '.') ?> €</td></tr>
            </tbody>
        </table>
    </div>
    <a href="checkout.php" class="btn btn-success">Procedi all'acquisto</a>
<?php else: ?>
    <p>Carrello vuoto. <a href="index.php">Vai al catalogo</a></p>
<?php endif; ?>
<?php include 'footer.php'; ?>
