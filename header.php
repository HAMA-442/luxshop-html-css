<?php
// header.php — Navigation principale LuxShop
if (session_status() === PHP_SESSION_NONE) session_start();

$panier_count = isset($_SESSION["panier"]["id"]) ? count($_SESSION["panier"]["id"]) : 0;
$user_name    = $_COOKIE["connected_user"] ?? null;
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxShop — Boutique Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ===== DESIGN TOKENS ===== */
        :root {
            --black:    #0A0A0A;
            --dark:     #141414;
            --card:     #1C1C1C;
            --border:   #2A2A2A;
            --gold:     #C9A84C;
            --gold-dim: #8A6E2F;
            --white:    #F0EDE8;
            --muted:    #7A7A7A;
            --danger:   #C0392B;
            --success:  #27AE60;
            --ff-serif: 'Cormorant Garamond', Georgia, serif;
            --ff-sans:  'Inter', system-ui, sans-serif;
            --radius:   6px;
            --shadow:   0 4px 24px rgba(0,0,0,0.5);
            --trans:    0.25s ease;
        }

        /* ===== RESET & BASE ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--black);
            color: var(--white);
            font-family: var(--ff-sans);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; transition: color var(--trans); }
        img { display: block; max-width: 100%; }
        input, button, select { font-family: var(--ff-sans); }

        /* ===== NAVIGATION ===== */
        .nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,10,10,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
        }
        .nav-inner {
            max-width: 1280px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            height: 70px;
        }
        .nav-logo {
            font-family: var(--ff-serif);
            font-size: 1.9rem; font-weight: 600;
            letter-spacing: 0.05em;
            color: var(--gold);
        }
        .nav-logo span { color: var(--white); font-weight: 300; }
        .nav-links {
            display: flex; gap: 2rem; align-items: center;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.82rem; font-weight: 400; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--muted);
        }
        .nav-links a:hover, .nav-links a.active { color: var(--gold); }
        .nav-right {
            display: flex; align-items: center; gap: 1.5rem;
        }
        .nav-user {
            font-size: 0.82rem; color: var(--muted);
        }
        .nav-user strong { color: var(--gold); }
        .nav-cart {
            position: relative; display: flex; align-items: center;
            gap: 0.5rem; padding: 0.5rem 1.1rem;
            border: 1px solid var(--gold-dim); border-radius: var(--radius);
            color: var(--white); font-size: 0.82rem;
            letter-spacing: 0.08em; transition: all var(--trans);
        }
        .nav-cart:hover { border-color: var(--gold); background: rgba(201,168,76,0.1); }
        .nav-cart-icon { font-size: 1.1rem; }
        .cart-badge {
            position: absolute; top: -8px; right: -8px;
            background: var(--gold); color: var(--black);
            border-radius: 50%; width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
        }
        .btn-auth {
            padding: 0.45rem 1.2rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.8rem; letter-spacing: 0.06em;
            cursor: pointer; transition: all var(--trans);
        }
        .btn-login  { background: transparent; color: var(--muted); }
        .btn-login:hover  { border-color: var(--gold); color: var(--gold); }
        .btn-logout { background: var(--danger); color: #fff; border-color: var(--danger); }
        .btn-logout:hover { opacity: 0.85; }

        /* ===== FLASH MESSAGES ===== */
        .flash {
            max-width: 1280px; margin: 1rem auto;
            padding: 0.8rem 1.5rem; border-radius: var(--radius);
            font-size: 0.88rem; display: flex; align-items: center; gap: 0.6rem;
        }
        .flash-success { background: rgba(39,174,96,0.15); border: 1px solid var(--success); color: #2ecc71; }
        .flash-error   { background: rgba(192,57,43,0.15); border: 1px solid var(--danger); color: #e74c3c; }

        /* ===== LAYOUT ===== */
        .container { max-width: 1280px; margin: 0 auto; padding: 0 2rem; }
        .page-title {
            font-family: var(--ff-serif); font-size: 2.4rem;
            font-weight: 300; letter-spacing: 0.04em;
            padding: 2.5rem 0 0.4rem;
        }
        .page-title span { color: var(--gold); }
        .page-subtitle { color: var(--muted); font-size: 0.88rem; margin-bottom: 2.5rem; }

        /* ===== PRODUCT GRID ===== */
        .filters {
            display: flex; gap: 0.6rem; flex-wrap: wrap;
            margin-bottom: 2rem; padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .filter-btn {
            padding: 0.4rem 1rem; border-radius: 20px;
            border: 1px solid var(--border); background: transparent;
            color: var(--muted); font-size: 0.8rem; cursor: pointer;
            transition: all var(--trans); letter-spacing: 0.06em;
        }
        .filter-btn:hover, .filter-btn.active {
            border-color: var(--gold); color: var(--gold);
            background: rgba(201,168,76,0.08);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.8rem; margin-bottom: 4rem;
        }

        /* ===== PRODUCT CARD ===== */
        .product-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden;
            transition: transform var(--trans), border-color var(--trans), box-shadow var(--trans);
            display: flex; flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-4px);
            border-color: var(--gold-dim);
            box-shadow: 0 12px 40px rgba(201,168,76,0.12);
        }
        .product-img-wrap {
            position: relative; overflow: hidden;
            background: var(--dark); aspect-ratio: 4/3;
        }
        .product-img-wrap img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.4s ease;
        }
        .product-card:hover .product-img-wrap img { transform: scale(1.04); }
        .product-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-size: 3.5rem; color: var(--border);
        }
        .badge {
            position: absolute; top: 12px; left: 12px;
            background: var(--gold); color: var(--black);
            font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em;
            text-transform: uppercase; padding: 0.25rem 0.65rem;
            border-radius: 3px;
        }
        .product-body { padding: 1.3rem; flex: 1; display: flex; flex-direction: column; }
        .product-cat {
            font-size: 0.72rem; color: var(--gold); letter-spacing: 0.12em;
            text-transform: uppercase; margin-bottom: 0.4rem;
        }
        .product-name {
            font-family: var(--ff-serif); font-size: 1.25rem;
            font-weight: 400; margin-bottom: 0.5rem; line-height: 1.3;
        }
        .product-desc {
            font-size: 0.82rem; color: var(--muted); line-height: 1.55;
            flex: 1; margin-bottom: 1rem;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-footer {
            display: flex; align-items: flex-end; justify-content: space-between;
            border-top: 1px solid var(--border); padding-top: 1rem;
        }
        .product-price {
            font-family: var(--ff-serif); font-size: 1.5rem;
            font-weight: 600; color: var(--gold);
        }
        .product-price small {
            font-family: var(--ff-sans); font-size: 0.75rem;
            color: var(--muted); font-weight: 300; margin-left: 2px;
        }
        .product-stock {
            font-size: 0.73rem; color: var(--muted); margin-top: 2px;
        }
        .product-stock.low { color: #e67e22; }

        /* ===== FORM ADD TO CART ===== */
        .add-form { margin-top: 0.8rem; }
        .qty-row {
            display: flex; gap: 0.6rem; align-items: center; margin-bottom: 0.7rem;
        }
        .qty-label { font-size: 0.78rem; color: var(--muted); white-space: nowrap; }
        .qty-input {
            width: 70px; padding: 0.4rem 0.6rem;
            background: var(--dark); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--white); font-size: 0.85rem;
            transition: border-color var(--trans);
        }
        .qty-input:focus { outline: none; border-color: var(--gold); }
        .btn-cart {
            width: 100%; padding: 0.65rem;
            background: transparent; border: 1px solid var(--gold-dim);
            border-radius: var(--radius); color: var(--gold);
            font-size: 0.82rem; letter-spacing: 0.1em; text-transform: uppercase;
            cursor: pointer; transition: all var(--trans);
        }
        .btn-cart:hover { background: var(--gold); color: var(--black); font-weight: 600; }
        .btn-cart:disabled { border-color: var(--border); color: var(--muted); cursor: not-allowed; }

        /* ===== PANIER PAGE ===== */
        .cart-layout {
            display: grid; grid-template-columns: 1fr 340px;
            gap: 2rem; align-items: start;
        }
        .cart-items { display: flex; flex-direction: column; gap: 1.2rem; }
        .cart-item {
            display: flex; gap: 1.2rem; align-items: center;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; padding: 1.2rem; transition: border-color var(--trans);
        }
        .cart-item:hover { border-color: var(--border); }
        .cart-item-img {
            width: 100px; height: 100px; border-radius: 6px;
            object-fit: cover; background: var(--dark); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: var(--border);
        }
        .cart-item-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 6px; }
        .cart-item-info { flex: 1; }
        .cart-item-name { font-family: var(--ff-serif); font-size: 1.15rem; margin-bottom: 0.3rem; }
        .cart-item-price { color: var(--gold); font-size: 1rem; margin-bottom: 0.6rem; }
        .cart-item-actions { display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap; }
        .cart-qty-input {
            width: 65px; padding: 0.35rem 0.5rem;
            background: var(--dark); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--white); font-size: 0.85rem;
        }
        .cart-qty-input:focus { outline: none; border-color: var(--gold); }
        .btn-update {
            padding: 0.35rem 0.9rem; border-radius: var(--radius);
            background: transparent; border: 1px solid var(--gold-dim);
            color: var(--gold); font-size: 0.78rem; cursor: pointer;
            transition: all var(--trans);
        }
        .btn-update:hover { background: var(--gold); color: var(--black); }
        .btn-delete {
            padding: 0.35rem 0.9rem; border-radius: var(--radius);
            background: transparent; border: 1px solid rgba(192,57,43,0.5);
            color: #e74c3c; font-size: 0.78rem; cursor: pointer;
            transition: all var(--trans);
        }
        .btn-delete:hover { background: var(--danger); color: #fff; border-color: var(--danger); }
        .cart-summary {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; padding: 1.8rem; position: sticky; top: 90px;
        }
        .summary-title {
            font-family: var(--ff-serif); font-size: 1.4rem;
            margin-bottom: 1.5rem; padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .summary-line {
            display: flex; justify-content: space-between;
            font-size: 0.88rem; margin-bottom: 0.8rem; color: var(--muted);
        }
        .summary-total {
            display: flex; justify-content: space-between;
            font-family: var(--ff-serif); font-size: 1.4rem;
            padding-top: 1rem; margin-top: 0.5rem;
            border-top: 1px solid var(--border); color: var(--gold);
        }
        .btn-checkout {
            width: 100%; margin-top: 1.5rem; padding: 0.9rem;
            background: var(--gold); color: var(--black);
            border: none; border-radius: var(--radius);
            font-size: 0.85rem; font-weight: 600; letter-spacing: 0.1em;
            text-transform: uppercase; cursor: pointer; transition: opacity var(--trans);
        }
        .btn-checkout:hover { opacity: 0.88; }
        .cart-empty {
            text-align: center; padding: 5rem 2rem;
            color: var(--muted);
        }
        .cart-empty-icon { font-size: 4rem; margin-bottom: 1.2rem; }
        .cart-empty h2 { font-family: var(--ff-serif); font-size: 1.8rem; color: var(--white); margin-bottom: 0.5rem; }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.4rem;
            margin-top: 1.5rem; color: var(--gold);
            font-size: 0.85rem; letter-spacing: 0.06em;
            border: 1px solid var(--gold-dim); border-radius: var(--radius);
            padding: 0.5rem 1.2rem; transition: all var(--trans);
        }
        .back-link:hover { background: rgba(201,168,76,0.1); }

        /* ===== LOGIN PAGE ===== */
        .auth-wrap {
            min-height: calc(100vh - 70px);
            display: flex; align-items: center; justify-content: center;
        }
        .auth-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 12px; padding: 3rem 2.5rem; width: 100%; max-width: 420px;
        }
        .auth-logo { font-family: var(--ff-serif); font-size: 2rem; color: var(--gold); text-align: center; margin-bottom: 0.3rem; }
        .auth-sub { text-align: center; color: var(--muted); font-size: 0.85rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-label { display: block; font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); margin-bottom: 0.4rem; }
        .form-input {
            width: 100%; padding: 0.75rem 1rem;
            background: var(--dark); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--white); font-size: 0.9rem;
            transition: border-color var(--trans);
        }
        .form-input:focus { outline: none; border-color: var(--gold); }
        .btn-submit {
            width: 100%; padding: 0.85rem; margin-top: 0.5rem;
            background: var(--gold); color: var(--black);
            border: none; border-radius: var(--radius);
            font-size: 0.85rem; font-weight: 600; letter-spacing: 0.1em;
            text-transform: uppercase; cursor: pointer; transition: opacity var(--trans);
        }
        .btn-submit:hover { opacity: 0.88; }
        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.83rem; color: var(--muted); }
        .auth-footer a { color: var(--gold); }

        /* ===== FOOTER ===== */
        .site-footer {
            border-top: 1px solid var(--border);
            padding: 2.5rem 2rem; margin-top: 4rem;
            display: flex; align-items: center; justify-content: space-between;
            max-width: 1280px; margin-left: auto; margin-right: auto;
        }
        .footer-brand { font-family: var(--ff-serif); color: var(--gold); font-size: 1.1rem; }
        .footer-text { font-size: 0.78rem; color: var(--muted); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .cart-layout { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .products-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 500px) {
            .products-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Lux<span>Shop</span></a>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Catalogue</a></li>
            <li><a href="panier.php" class="<?= $current_page === 'panier.php' ? 'active' : '' ?>">Mon Panier</a></li>
        </ul>
        <div class="nav-right">
            <?php if ($user_name): ?>
                <span class="nav-user">Bonjour, <strong><?= htmlspecialchars($user_name) ?></strong></span>
                <a href="logout.php" class="btn-auth btn-logout">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-auth btn-login">Connexion</a>
            <?php endif; ?>
            <a href="panier.php" class="nav-cart">
                <span class="nav-cart-icon">🛒</span>
                <span>Panier</span>
                <?php if ($panier_count > 0): ?>
                    <span class="cart-badge"><?= $panier_count ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>
