<?php
// panier.php — Page du panier
session_start();
include "connexion.php";

// Initialiser le panier si besoin
if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [
        "id" => [], "name" => [], "price" => [],
        "description" => [], "image" => [],
        "stock_quantity" => [], "chosen_quantity" => []
    ];
}

// Traitement des actions POST (update / delete)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = (int)($_POST["product_id"] ?? 0);
    $index = array_search($product_id, $_SESSION["panier"]["id"]);

    if ($index !== false) {
        if (isset($_POST["update"])) {
            $new_qty = max(1, (int)($_POST["selected_quantity"] ?? 1));
            $old_qty = $_SESSION["panier"]["chosen_quantity"][$index];
            $diff    = $new_qty - $old_qty;

            // Vérifier le stock disponible
            if ($diff > $_SESSION["panier"]["stock_quantity"][$index]) {
                $_SESSION["flash"] = ["type" => "error", "msg" => "Quantité demandée non disponible en stock."];
            } else {
                $_SESSION["panier"]["stock_quantity"][$index]  -= $diff;
                $_SESSION["panier"]["chosen_quantity"][$index]  = $new_qty;
                $_SESSION["flash"] = ["type" => "success", "msg" => "Quantité mise à jour."];
            }
        }

        if (isset($_POST["delete"])) {
            $keys = ["id","name","price","description","image","stock_quantity","chosen_quantity"];
            foreach ($keys as $k) {
                array_splice($_SESSION["panier"][$k], $index, 1);
            }
            $_SESSION["flash"] = ["type" => "success", "msg" => "Article supprimé du panier."];
        }
    }

    header("Location: panier.php");
    exit;
}

$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

include "header.php";
?>

<div class="container">
    <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>" style="margin-top:1.5rem;">
        <?= $flash['type'] === 'success' ? '✓' : '✗' ?> <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <h1 class="page-title">Mon <span>Panier</span></h1>
    <p class="page-subtitle">
        <?= count($_SESSION["panier"]["id"]) ?> article(s) sélectionné(s)
    </p>

    <?php if (empty($_SESSION["panier"]["id"])): ?>
    <div class="cart-empty">
        <div class="cart-empty-icon">🛒</div>
        <h2>Votre panier est vide</h2>
        <p>Découvrez notre collection et ajoutez vos articles préférés.</p>
        <a href="index.php" class="back-link">← Voir le catalogue</a>
    </div>
    <?php else: 
        $total = 0;
        for ($i = 0; $i < count($_SESSION["panier"]["id"]); $i++) {
            $total += $_SESSION["panier"]["price"][$i] * $_SESSION["panier"]["chosen_quantity"][$i];
        }
    ?>
    <div class="cart-layout">
        <!-- Articles -->
        <div class="cart-items">
            <?php for ($i = 0; $i < count($_SESSION["panier"]["id"]); $i++): 
                $subtotal = $_SESSION["panier"]["price"][$i] * $_SESSION["panier"]["chosen_quantity"][$i];
                $max_qty  = $_SESSION["panier"]["chosen_quantity"][$i] + $_SESSION["panier"]["stock_quantity"][$i];
            ?>
            <div class="cart-item">
                <div class="cart-item-img">🛍️</div>
                <div class="cart-item-info">
                    <div class="cart-item-name"><?= htmlspecialchars($_SESSION["panier"]["name"][$i]) ?></div>
                    <div class="cart-item-price">
                        <?= number_format($_SESSION["panier"]["price"][$i], 3, '.', ' ') ?> DT × <?= $_SESSION["panier"]["chosen_quantity"][$i] ?>
                        = <strong><?= number_format($subtotal, 3, '.', ' ') ?> DT</strong>
                    </div>
                    <form method="POST" action="panier.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $_SESSION["panier"]["id"][$i] ?>">
                        <div class="cart-item-actions">
                            <input type="number" name="selected_quantity" class="cart-qty-input"
                                   min="1" max="<?= $max_qty ?>"
                                   value="<?= $_SESSION["panier"]["chosen_quantity"][$i] ?>">
                            <button type="submit" name="update" class="btn-update">Mettre à jour</button>
                            <button type="submit" name="delete" class="btn-delete"
                                    onclick="return confirm('Supprimer cet article ?')">Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endfor; ?>

            <a href="index.php" class="back-link">← Continuer vos achats</a>
        </div>

        <!-- Récapitulatif -->
        <div class="cart-summary">
            <h2 class="summary-title">Récapitulatif</h2>
            <?php for ($i = 0; $i < count($_SESSION["panier"]["id"]); $i++): ?>
            <div class="summary-line">
                <span><?= htmlspecialchars($_SESSION["panier"]["name"][$i]) ?> ×<?= $_SESSION["panier"]["chosen_quantity"][$i] ?></span>
                <span><?= number_format($_SESSION["panier"]["price"][$i] * $_SESSION["panier"]["chosen_quantity"][$i], 3, '.', ' ') ?> DT</span>
            </div>
            <?php endfor; ?>
            <div class="summary-line">
                <span>Livraison estimée</span>
                <span><?= $total >= 500 ? '<span style="color:var(--success)">Gratuite</span>' : '8.000 DT' ?></span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span><?= number_format($total + ($total >= 500 ? 0 : 8), 3, '.', ' ') ?> DT</span>
            </div>
            <?php if ($total < 500): ?>
            <p style="font-size:0.78rem; color:var(--muted); margin-top:0.8rem; text-align:center;">
                Plus que <?= number_format(500 - $total, 3, '.', ' ') ?> DT pour la livraison gratuite
            </p>
            <?php endif; ?>
            <button class="btn-checkout" onclick="alert('Fonctionnalité de paiement bientôt disponible !')">
                Passer la commande →
            </button>
            <p style="font-size:0.75rem; color:var(--muted); text-align:center; margin-top:1rem;">
                🔒 Paiement sécurisé SSL
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>

<footer style="border-top:1px solid var(--border); padding:2rem; margin-top:4rem; text-align:center;">
    <div class="footer-brand">LuxShop</div>
    <p class="footer-text" style="margin-top:0.5rem;">© 2024 LuxShop Tunisia — Tous droits réservés</p>
</footer>

</body>
</html>
