<?php
// index.php — Catalogue produits LuxShop
session_start();
include "connexion.php";

// Initialisation du panier en session
if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [
        "id"               => [],
        "name"             => [],
        "price"            => [],
        "description"      => [],
        "image"            => [],
        "stock_quantity"   => [],
        "chosen_quantity"  => [],
    ];
}

// Récupérer les catégories pour les filtres
$cats = $pdo->query("SELECT * FROM categorie ORDER BY nom")->fetchAll();

// Filtre catégorie
$cat_filter = isset($_GET["cat"]) ? (int)$_GET["cat"] : 0;
$search     = isset($_GET["q"]) ? trim($_GET["q"]) : "";

// Requête produits
$sql    = "SELECT a.*, c.nom AS cat_nom, c.icone FROM article a LEFT JOIN categorie c ON a.categorie_id = c.id WHERE 1=1";
$params = [];
if ($cat_filter) { $sql .= " AND a.categorie_id = ?"; $params[] = $cat_filter; }
if ($search)     { $sql .= " AND a.name LIKE ?";      $params[] = "%$search%"; }
$sql .= " ORDER BY a.id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Flash message
$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

include "header.php";
?>

<?php if ($flash): ?>
<div class="container">
    <div class="flash flash-<?= $flash['type'] ?>">
        <?= $flash['type'] === 'success' ? '✓' : '✗' ?> <?= htmlspecialchars($flash['msg']) ?>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <h1 class="page-title">Notre <span>Collection</span></h1>
    <p class="page-subtitle">Sélection de produits premium, choisis avec soin pour vous.</p>

    <!-- Barre de recherche + filtres -->
    <form method="GET" action="index.php" style="margin-bottom:1.5rem; display:flex; gap:0.8rem; align-items:center;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Rechercher un produit…" class="form-input" style="max-width:320px; padding:0.5rem 1rem;">
        <?php if ($cat_filter): ?><input type="hidden" name="cat" value="<?= $cat_filter ?>"><?php endif; ?>
        <button type="submit" class="btn-cart" style="width:auto; padding:0.5rem 1.2rem;">Chercher</button>
        <?php if ($search || $cat_filter): ?>
            <a href="index.php" style="font-size:0.8rem; color:var(--muted);">✕ Effacer</a>
        <?php endif; ?>
    </form>

    <!-- Filtres catégories -->
    <div class="filters">
        <a href="index.php" class="filter-btn <?= !$cat_filter ? 'active' : '' ?>">Tout voir</a>
        <?php foreach ($cats as $cat): ?>
            <a href="?cat=<?= $cat['id'] ?><?= $search ? '&q='.urlencode($search) : '' ?>"
               class="filter-btn <?= $cat_filter === $cat['id'] ? 'active' : '' ?>">
                <?= $cat['icone'] ?> <?= htmlspecialchars($cat['nom']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Grille produits -->
    <?php if (empty($products)): ?>
        <div class="cart-empty" style="padding:4rem 2rem;">
            <div class="cart-empty-icon">🔍</div>
            <h2>Aucun produit trouvé</h2>
            <p>Modifiez votre recherche ou naviguez par catégorie.</p>
        </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $p): 
            $in_cart = in_array($p['id'], $_SESSION["panier"]["id"]);
            $low     = $p['stock_quantity'] > 0 && $p['stock_quantity'] <= 5;
        ?>
        <div class="product-card">
            <div class="product-img-wrap">
                <?php if ($p['badge']): ?><span class="badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
                <div class="product-placeholder"><?= $p['icone'] ?? '🛍️' ?></div>
            </div>
            <div class="product-body">
                <div class="product-cat"><?= htmlspecialchars($p['cat_nom'] ?? '') ?></div>
                <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
                <p class="product-desc"><?= htmlspecialchars($p['description']) ?></p>

                <div class="product-footer">
                    <div>
                        <div class="product-price"><?= number_format($p['price'], 3, '.', ' ') ?><small>DT</small></div>
                        <?php if ($p['stock_quantity'] <= 0): ?>
                            <div class="product-stock low">Rupture de stock</div>
                        <?php elseif ($low): ?>
                            <div class="product-stock low">⚠ Plus que <?= $p['stock_quantity'] ?> en stock</div>
                        <?php else: ?>
                            <div class="product-stock"><?= $p['stock_quantity'] ?> disponibles</div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$in_cart && $p['stock_quantity'] > 0): ?>
                <form class="add-form" method="POST" action="add_to_cart.php">
                    <input type="hidden" name="selected_id" value="<?= $p['id'] ?>">
                    <div class="qty-row">
                        <label class="qty-label" for="qty_<?= $p['id'] ?>">Qté :</label>
                        <input type="number" id="qty_<?= $p['id'] ?>" name="selected_quantity"
                               min="1" max="<?= $p['stock_quantity'] ?>" value="1" class="qty-input">
                    </div>
                    <button type="submit" class="btn-cart">+ Ajouter au panier</button>
                </form>
                <?php elseif ($in_cart): ?>
                    <div style="margin-top:0.8rem; text-align:center; font-size:0.8rem; color:var(--gold); border:1px solid var(--gold-dim); border-radius:var(--radius); padding:0.5rem;">
                        ✓ Déjà dans le panier
                    </div>
                <?php else: ?>
                    <button class="btn-cart" disabled style="margin-top:0.8rem;">Indisponible</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<footer style="border-top:1px solid var(--border); padding:2rem; margin-top:4rem; text-align:center;">
    <div class="footer-brand">LuxShop</div>
    <p class="footer-text" style="margin-top:0.5rem;">© 2024 LuxShop Tunisia — Tous droits réservés</p>
</footer>

</body>
</html>
