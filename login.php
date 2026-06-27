<?php


session_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'config.php';

// ── Vérification de la méthode HTTP ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

// ── Récupération et nettoyage des données ────────────────────────────────────
$email     = trim($_POST['email']     ?? '');
$mot_passe = trim($_POST['mot_passe'] ?? '');

// ── Validation de base ───────────────────────────────────────────────────────
if (empty($email) || empty($mot_passe)) {
    echo "<p style='font-size:20px; color:red;'>Veuillez remplir tous les champs.</p>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<p style='font-size:20px; color:red;'>Adresse e-mail invalide.</p>";
    exit();
}

// ── Connexion à la base de données ───────────────────────────────────────────
$link = getConnection();

// ── Requête préparée (protection SQL Injection) ──────────────────────────────
$stmt = mysqli_prepare($link, "SELECT Id_Clt, No_Clt, Pno_Clt, Mot_Clt FROM client WHERE Mail_Clt = ?");

if (!$stmt) {
    error_log("Erreur prepare login : " . mysqli_error($link));
    echo "<p style='font-size:20px; color:red;'>Erreur serveur. Veuillez réessayer.</p>";
    mysqli_close($link);
    exit();
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ── Vérification de l'utilisateur ───────────────────────────────────────────
if ($row = mysqli_fetch_assoc($result)) {
    // Vérification du mot de passe haché
    if (password_verify($mot_passe, $row['Mot_Clt'])) {
        // Régénérer l'ID de session (protection fixation de session)
        session_regenerate_id(true);

        $_SESSION['id']      = $row['Id_Clt'];
        $_SESSION['No_Clt']  = $row['No_Clt'];
        $_SESSION['Pno_Clt'] = $row['Pno_Clt'];

        echo "<p style='font-size:20px; color:green;'>
                Bonjour <strong>{$row['No_Clt']} {$row['Pno_Clt']}</strong> ! Connexion réussie.
              </p>";

        // Redirection vers la page principale après 2 secondes
        echo "<script>setTimeout(() => window.location.href = 'index.html', 2000);</script>";
    } else {
        echo "<p style='font-size:20px; color:red;'>E-mail et/ou mot de passe incorrect(s).</p>";
    }
} else {
    echo "<p style='font-size:20px; color:red;'>E-mail et/ou mot de passe incorrect(s).</p>";
}

// ── Nettoyage ────────────────────────────────────────────────────────────────
mysqli_stmt_close($stmt);
mysqli_close($link);
