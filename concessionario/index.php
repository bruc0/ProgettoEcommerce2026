<?php
require_once 'config.php';

$where = ["NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.car_id = autos.id)"];
$params = [];

if(!empty($_GET['make'])) {
    $where[] = "marca = ?";
    $params[] = $_GET['make'];
}
if(!empty($_GET['model'])) {
    $where[] = "modello LIKE ?";
    $params[] = '%' . $_GET['model'] . '%';
}
if(!empty($_GET['fuel'])) {
    $where[] = "carburante = ?";
    $params[] = $_GET['fuel'];
}
if(!empty($_GET['min_price'])) {
    $where[] = "prezzo >= ?";
    $params[] = $_GET['min_price'];
}
if(!empty($_GET['max_price'])) {
    $where[] = "prezzo <= ?";
    $params[] = $_GET['max_price'];
}
if(!empty($_GET['max_mileage'])) {
    $where[] = "chilometraggio <= ?";
    $params[] = $_GET['max_mileage'];
}
if(!empty($_GET['year_from'])) {
    $where[] = "YEAR(immatricolazione) >= ?";
    $params[] = $_GET['year_from'];
}

$sql = "SELECT * FROM autos WHERE " . implode(" AND ", $where) . " ORDER BY prezzo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$makes = $pdo->query("SELECT DISTINCT marca FROM autos ORDER BY marca")->fetchAll();

include 'header.php';
?>
<div class="row">
    <div class="col-md-3">
        <div class="filter-sidebar">
            <h5><i class="bi bi-funnel"></i> Filtri</h5>
            <form method="get" action="">
                <div class="mb-3">
                    <label>Marca</label>
                    <select name="make" class="form-select" onchange="this.form.submit()">
                        <option value="">Tutte</option>
                        <?php foreach($makes as $m): ?>
                            <option value="<?= htmlspecialchars($m['marca']) ?>" <?= isset($_GET['make']) && $_GET['make']==$m['marca'] ? 'selected' : '' ?>><?= htmlspecialchars($m['marca']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Modello</label>
                    <input type="text" name="model" class="form-control" value="<?= htmlspecialchars($_GET['model'] ?? '') ?>" placeholder="es. Golf">
                </div>
                <div class="mb-3">
                    <label>Carburante</label>
                    <select name="fuel" class="form-select" onchange="this.form.submit()">
                        <option value="">Qualsiasi</option>
                        <option value="Benzina" <?= ($_GET['fuel']??'')=='Benzina'?'selected':'' ?>>Benzina</option>
                        <option value="Diesel" <?= ($_GET['fuel']??'')=='Diesel'?'selected':'' ?>>Diesel</option>
                        <option value="Elettrica" <?= ($_GET['fuel']??'')=='Elettrica'?'selected':'' ?>>Elettrica</option>
                        <option value="Ibrida" <?= ($_GET['fuel']??'')=='Ibrida'?'selected':'' ?>>Ibrida</option>
                        <option value="GPL" <?= ($_GET['fuel']??'')=='GPL'?'selected':'' ?>>GPL</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Prezzo (€)</label>
                    <div class="row">
                        <div class="col"><input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"></div>
                        <div class="col"><input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Chilometraggio max (km)</label>
                    <input type="number" name="max_mileage" class="form-control" value="<?= htmlspecialchars($_GET['max_mileage'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Immatricolazione da</label>
                    <input type="number" name="year_from" class="form-control" placeholder="Anno" value="<?= htmlspecialchars($_GET['year_from'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary w-100">Applica filtri</button>
                <a href="index.php" class="btn btn-secondary w-100 mt-2">Reset</a>
            </form>
        </div>
    </div>

    <div class="col-md-9">
        <h4><?= count($cars) ?> auto trovate</h4>
        <div class="row">
            <?php foreach($cars as $car): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 car-card">
                    <img src="https://via.placeholder.com/450x300?text=Auto" class="card-img-top" alt="<?= htmlspecialchars($car['marca'].' '.$car['modello']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($car['marca'] . ' ' . $car['modello']) ?></h5>
                        <p class="card-text">
                            <strong><?= number_format((float) $car['prezzo'], 0, ',', '.') ?> €</strong><br>
                            <?= date('Y', strtotime($car['immatricolazione'])) ?> · <?= number_format((float) $car['chilometraggio'], 0, ',', '.') ?> km<br>
                            <?= htmlspecialchars($car['carburante']) ?> · <?= (int) $car['potenza_cv'] ?> CV<br>
                            <small class="text-muted"><?= htmlspecialchars($car['colore']) ?> · <?= (int) $car['porte'] ?> porte</small>
                        </p>
                        <?php if(currentUserId() !== null): ?>
                            <a href="add_to_cart.php?car_id=<?= $car['id'] ?>" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-cart-plus"></i> Aggiungi al carrello</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-secondary btn-sm w-100">Accedi per acquistare</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(count($cars)==0): ?>
                <div class="alert alert-info">Nessuna auto trovata con i filtri selezionati.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
