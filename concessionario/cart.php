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
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title h2 mb-1">Il mio carrello</h1>
        <p class="text-muted mb-0">Controlla le auto selezionate prima di procedere all'acquisto.</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Torna al catalogo</a>
</div>

<?php if(count($cartItems) > 0): ?>
    <div class="card content-card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Auto</th>
                            <th>Dettagli</th>
                            <th class="text-end">Prezzo</th>
                            <th class="text-end">Azione</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($item['marca'] . ' ' . $item['modello']) ?></td>
                                <td class="text-muted small"><?= date('Y', strtotime($item['immatricolazione'])) ?> · <?= number_format((float) $item['chilometraggio'], 0, ',', '.') ?> km · <?= htmlspecialchars($item['carburante']) ?></td>
                                <td class="text-end fw-semibold"><?= number_format((float) $item['prezzo'], 2, ',', '.') ?> €</td>
                                <td class="text-end"><a href="remove_from_cart.php?car_id=<?= $item['car_id'] ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Rimuovi</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-bold">Totale</td>
                            <td colspan="2" class="text-end fw-bold"><?= number_format((float) $total, 2, ',', '.') ?> €</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <a href="checkout.php" class="btn btn-success btn-lg"><i class="bi bi-check2-circle me-1"></i>Procedi all'acquisto</a>
    </div>
<?php else: ?>
    <div class="empty-state rounded-3 p-5 text-center">
        <i class="bi bi-cart-x display-5 text-muted"></i>
        <h2 class="h4 mt-3">Carrello vuoto</h2>
        <p class="text-muted mb-4">Aggiungi una vettura dal catalogo per iniziare l'acquisto.</p>
        <a href="index.php" class="btn btn-primary">Vai al catalogo</a>
    </div>
<?php endif; ?>
<?php include 'footer.php'; ?>
