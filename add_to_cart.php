<?php
// add_to_cart.php — Ajouter un produit au panier
session_start();
include "connexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["selected_id"])) {
    header("Location: index.php");
    exit;
}

$id  = (int)$_POST["selected_id"];
$qty = max(1, (int)($_POST["selected_quantity"] ?? 1));

// Vérifier que le produit existe et a du stock
$stmt = $pdo->prepare("SELECT * FROM article WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION["flash"] = ["type" => "error", "msg" => "Produit introuvable."];
    header("Location: index.php");
    exit;
}

if ($product["stock_quantity"] < $qty) {
    $_SESSION["flash"] = ["type" => "error", "msg" => "Stock insuffisant (disponible : " . $product["stock_quantity"] . ")."];
    header("Location: index.php");
    exit;
}

// Vérifier si déjà dans le panier
if (in_array($id, $_SESSION["panier"]["id"])) {
    $_SESSION["flash"] = ["type" => "error", "msg" => "Ce produit est déjà dans votre panier."];
    header("Location: index.php");
    exit;
}

// Initialisation panier si besoin
if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [
        "id" => [], "name" => [], "price" => [],
        "description" => [], "image" => [],
        "stock_quantity" => [], "chosen_quantity" => []
    ];
}

// Ajouter au panier
array_push($_SESSION["panier"]["id"],             $product["id"]);
array_push($_SESSION["panier"]["name"],           $product["name"]);
array_push($_SESSION["panier"]["price"],          $product["price"]);
array_push($_SESSION["panier"]["description"],    $product["description"]);
array_push($_SESSION["panier"]["image"],          $product["image"]);
array_push($_SESSION["panier"]["stock_quantity"], $product["stock_quantity"] - $qty);
array_push($_SESSION["panier"]["chosen_quantity"], $qty);

$_SESSION["flash"] = ["type" => "success", "msg" => "« " . $product["name"] . " » ajouté au panier !"];
header("Location: index.php");
exit;
