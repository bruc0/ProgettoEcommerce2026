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
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title h2 mb-1">I miei ordini</h1>
        <p class="text-muted mb-0">Consulta gli acquisti completati.</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-plus-circle me-1"></i>Acquista ancora</a>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle me-1"></i>Acquisto completato con successo!</div>
<?php endif; ?>

<?php if(count($orders) > 0): ?>
    <div class="vstack gap-3">
        <?php foreach($orders as $order): ?>
            <div class="card content-card shadow-sm">
                <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between gap-2">
                    <div>
                        <span class="fw-semibold">Ordine #<?= $order['id'] ?></span>
                        <span class="text-muted ms-md-2"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></span>
                    </div>
                    <span class="badge text-bg-success align-self-start align-self-md-center"><?= number_format((float) $order['total_amount'], 2, ',', '.') ?> €</span>
                </div>
                <div class="card-body">
                    <?php
                    $stmtItems = $pdo->prepare("SELECT oi.*, a.marca, a.modello, a.immatricolazione FROM order_items oi JOIN autos a ON oi.car_id = a.id WHERE oi.order_id = ?");
                    $stmtItems->execute([$order['id']]);
                    $items = $stmtItems->fetchAll();
                    ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach($items as $item): ?>
                            <li class="list-group-item px-0 d-flex justify-content-between gap-3">
                                <span><?= htmlspecialchars($item['marca'] . ' ' . $item['modello']) ?> <span class="text-muted">(<?= date('Y', strtotime($item['immatricolazione'])) ?>)</span></span>
                                <span class="fw-semibold text-nowrap"><?= number_format((float) $item['price_at_purchase'], 2, ',', '.') ?> €</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state rounded-3 p-5 text-center">
        <i class="bi bi-receipt display-5 text-muted"></i>
        <h2 class="h4 mt-3">Nessun ordine</h2>
        <p class="text-muted mb-4">Non hai ancora effettuato acquisti.</p>
        <a href="index.php" class="btn btn-primary">Acquista ora</a>
    </div>
<?php endif; ?>
<?php include 'footer.php'; ?>
