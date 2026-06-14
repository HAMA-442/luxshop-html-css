<?php
// logout.php — Déconnexion
session_start();
session_destroy();
setcookie("connected_user", "", time() - 3600, "/");
setcookie("user_role",      "", time() - 3600, "/");
header("Location: index.php");
exit;
