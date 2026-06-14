<?php
// register.php — Inscription
session_start();
include "connexion.php";

if (isset($_COOKIE["connected_user"])) {
    header("Location: index.php");
    exit;
}

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom    = trim($_POST["nom"]      ?? "");
    $prenom = trim($_POST["prenom"]   ?? "");
    $email  = trim($_POST["email"]    ?? "");
    $pass   = $_POST["password"]      ?? "";
    $pass2  = $_POST["password2"]     ?? "";

    if (!$nom || !$prenom || !$email || !$pass) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($pass !== $pass2) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($pass) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        // Vérifier si email existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins  = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
            $ins->execute([$nom, $prenom, $email, $hash]);
            $_SESSION["flash"] = ["type" => "success", "msg" => "Compte créé ! Vous pouvez maintenant vous connecter."];
            header("Location: login.php");
            exit;
        }
    }
}

include "header.php";
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">LuxShop</div>
        <p class="auth-sub">Créez votre compte premium</p>

        <?php if ($error): ?>
        <div class="flash flash-error" style="margin-bottom:1.5rem;">✗ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.8rem;">
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-input"
                           placeholder="Aymen" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-input"
                           placeholder="Ben Salem" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-input"
                       placeholder="vous@exemple.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-input" placeholder="Min. 6 caractères" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password2" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>

        <div class="auth-footer">
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

</body>
</html>
