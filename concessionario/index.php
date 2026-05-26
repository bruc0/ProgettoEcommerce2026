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

$hasAutoImages = $pdo->query("SHOW TABLES LIKE 'auto_images'")->fetch() !== false;
$imageSelect = $hasAutoImages
    ? ",(
            SELECT ai.image_url
            FROM auto_images ai
            WHERE ai.auto_id = autos.id
            ORDER BY ai.is_cover DESC, ai.id ASC
            LIMIT 1
        ) AS image_url"
    : ", NULL AS image_url";

$sql = "SELECT autos.*" . $imageSelect . "
        FROM autos
        WHERE " . implode(" AND ", $where) . "
        ORDER BY prezzo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$makes = $pdo->query("SELECT DISTINCT marca FROM autos ORDER BY marca")->fetchAll();

include 'header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <span class="badge text-bg-primary mb-2">Usato garantito</span>
        <h1 class="page-title h2 mb-1">Catalogo auto</h1>
        <p class="text-muted mb-0">Sfoglia le vetture disponibili e filtra in base alle tue preferenze.</p>
    </div>
    <div class="text-md-end">
        <span class="badge rounded-pill text-bg-light border"><?= count($cars) ?> auto trovate</span>
    </div>
</div>

<div class="row g-4 align-items-start">
    <aside class="col-lg-3">
        <div class="card shadow-sm filter-sidebar">
            <div class="card-body">
                <h2 class="h5 card-title mb-3"><i class="bi bi-funnel me-2"></i>Filtri</h2>
                <form method="get" action="index.php">
                    <div class="mb-3">
                        <label for="make" class="form-label">Marca</label>
                        <select id="make" name="make" class="form-select" onchange="this.form.submit()">
                            <option value="">Tutte</option>
                            <?php foreach($makes as $m): ?>
                                <option value="<?= htmlspecialchars($m['marca']) ?>" <?= isset($_GET['make']) && $_GET['make']==$m['marca'] ? 'selected' : '' ?>><?= htmlspecialchars($m['marca']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model" class="form-label">Modello</label>
                        <input id="model" type="text" name="model" class="form-control" value="<?= htmlspecialchars($_GET['model'] ?? '') ?>" placeholder="es. Golf">
                    </div>
                    <div class="mb-3">
                        <label for="fuel" class="form-label">Carburante</label>
                        <select id="fuel" name="fuel" class="form-select" onchange="this.form.submit()">
                            <option value="">Qualsiasi</option>
                            <option value="Benzina" <?= ($_GET['fuel']??'')=='Benzina'?'selected':'' ?>>Benzina</option>
                            <option value="Diesel" <?= ($_GET['fuel']??'')=='Diesel'?'selected':'' ?>>Diesel</option>
                            <option value="Elettrica" <?= ($_GET['fuel']??'')=='Elettrica'?'selected':'' ?>>Elettrica</option>
                            <option value="Ibrida" <?= ($_GET['fuel']??'')=='Ibrida'?'selected':'' ?>>Ibrida</option>
                            <option value="GPL" <?= ($_GET['fuel']??'')=='GPL'?'selected':'' ?>>GPL</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prezzo</label>
                        <div class="row g-2">
                            <div class="col"><input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"></div>
                            <div class="col"><input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="max_mileage" class="form-label">Chilometraggio max</label>
                        <input id="max_mileage" type="number" name="max_mileage" class="form-control" value="<?= htmlspecialchars($_GET['max_mileage'] ?? '') ?>" placeholder="km">
                    </div>
                    <div class="mb-4">
                        <label for="year_from" class="form-label">Immatricolazione da</label>
                        <input id="year_from" type="number" name="year_from" class="form-control" placeholder="Anno" value="<?= htmlspecialchars($_GET['year_from'] ?? '') ?>">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Applica filtri</button>
                        <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </aside>

    <section class="col-lg-9">
        <?php if(count($cars) > 0): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                <?php foreach($cars as $car): ?>
                    <?php $imageUrl = $car['image_url'] ?: 'https://via.placeholder.com/450x300?text=Auto'; ?>
                    <div class="col">
                        <article class="card h-100 car-card shadow-sm">
                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['marca'].' '.$car['modello']) ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h2 class="h5 card-title mb-0"><?= htmlspecialchars($car['marca'] . ' ' . $car['modello']) ?></h2>
                                    <span class="badge text-bg-light border"><?= date('Y', strtotime($car['immatricolazione'])) ?></span>
                                </div>
                                <p class="price-badge mb-3"><?= number_format((float) $car['prezzo'], 0, ',', '.') ?> €</p>
                                <div class="row g-2 small text-muted mb-3">
                                    <div class="col-6"><i class="bi bi-speedometer2 me-1"></i><?= number_format((float) $car['chilometraggio'], 0, ',', '.') ?> km</div>
                                    <div class="col-6"><i class="bi bi-fuel-pump me-1"></i><?= htmlspecialchars($car['carburante']) ?></div>
                                    <div class="col-6"><i class="bi bi-lightning-charge me-1"></i><?= (int) $car['potenza_cv'] ?> CV</div>
                                    <div class="col-6"><i class="bi bi-palette me-1"></i><?= htmlspecialchars($car['colore']) ?></div>
                                </div>
                                <p class="small text-muted mb-4"><?= (int) $car['porte'] ?> porte · <?= htmlspecialchars($car['tipo_venditore']) ?></p>
                                <div class="mt-auto">
                                    <?php if(currentUserId() !== null): ?>
                                        <a href="add_to_cart.php?car_id=<?= $car['id'] ?>" class="btn btn-outline-primary w-100"><i class="bi bi-cart-plus me-1"></i>Aggiungi al carrello</a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-secondary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Accedi per acquistare</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state rounded-3 p-5 text-center">
                <i class="bi bi-search display-5 text-muted"></i>
                <h2 class="h4 mt-3">Nessuna auto trovata</h2>
                <p class="text-muted mb-4">Modifica i filtri o torna al catalogo completo.</p>
                <a href="index.php" class="btn btn-primary">Mostra tutte le auto</a>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php include 'footer.php'; ?>
