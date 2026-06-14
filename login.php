<?php
// login.php — Page de connexion
session_start();
include "connexion.php";

// Déjà connecté
if (isset($_COOKIE["connected_user"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $pass  = $_POST["password"] ?? "";

    if (!$email || !$pass) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user["mot_de_passe"])) {
            // Connexion réussie — cookie 7 jours
            setcookie("connected_user", $user["prenom"] . " " . $user["nom"], time() + 7*24*3600, "/");
            setcookie("user_role",      $user["role"],                        time() + 7*24*3600, "/");
            $_SESSION["flash"] = ["type" => "success", "msg" => "Bienvenue, " . $user["prenom"] . " !"];
            header("Location: index.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

include "header.php";
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">LuxShop</div>
        <p class="auth-sub">Connectez-vous pour accéder à votre espace</p>

        <?php if ($error): ?>
        <div class="flash flash-error" style="margin-bottom:1.5rem;">✗ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label class="form-label" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" class="form-input"
                       placeholder="vous@exemple.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="auth-footer">
            <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
            <p style="margin-top:1rem; font-size:0.75rem; color:var(--border);">
                Compte démo : admin@luxshop.tn / password
            </p>
        </div>
    </div>
</div>

</body>
</html>
