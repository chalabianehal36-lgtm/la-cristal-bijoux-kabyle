<?php
/**
 * inscription.php — Création d'un compte client
 * La Cristal des Bijoux Kabyle
 */

session_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'config.php';

// ── Vérification de la méthode HTTP ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.html');
    exit();
}

// ── Récupération et nettoyage des données ────────────────────────────────────
$nom       = trim($_POST['nomm']       ?? '');
$prenom    = trim($_POST['prenom']     ?? '');
$age       = intval($_POST['agee']     ?? 0);
$wilaya    = trim($_POST['wilaya']     ?? '');
$telephone = trim($_POST['telephone']  ?? '');
$email     = trim($_POST['emaill']     ?? '');
$address   = trim($_POST['addresss']   ?? '');
$password  = $_POST['passwordd']       ?? '';
$sex       = trim($_POST['sexe']       ?? '');

// ── Validation des champs ─────────────────────────────────────────────────────
$errors = [];

if (empty($nom) || strlen($nom) > 50)
    $errors[] = "Nom invalide.";

if (empty($prenom) || strlen($prenom) > 50)
    $errors[] = "Prénom invalide.";

if ($age < 17 || $age > 100)
    $errors[] = "L'âge doit être compris entre 17 et 100 ans.";

if (empty($wilaya))
    $errors[] = "Veuillez choisir une wilaya.";

if (!preg_match('/^[0-9]{9,10}$/', $telephone))
    $errors[] = "Numéro de téléphone invalide.";

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Adresse e-mail invalide.";

if (empty($address))
    $errors[] = "Adresse requise.";

if (strlen($password) < 8)
    $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";

if (!in_array($sex, ['Homme', 'Femme']))
    $errors[] = "Veuillez sélectionner un sexe valide.";

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<p style='font-size:16px; color:red;'>⚠ $err</p>";
    }
    exit();
}

// ── Connexion à la base de données ───────────────────────────────────────────
$link = getConnection();

// ── Vérification si l'e-mail existe déjà ────────────────────────────────────
$stmtCheck = mysqli_prepare($link, "SELECT Id_Clt FROM client WHERE Mail_Clt = ?");
mysqli_stmt_bind_param($stmtCheck, 's', $email);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
    echo "<p style='font-size:20px; color:red;'>Cet e-mail est déjà utilisé.</p>";
    mysqli_stmt_close($stmtCheck);
    mysqli_close($link);
    exit();
}
mysqli_stmt_close($stmtCheck);

// ── Hachage du mot de passe (sécurité) ───────────────────────────────────────
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// ── Insertion avec requête préparée ──────────────────────────────────────────
$stmt = mysqli_prepare($link,
    "INSERT INTO client (No_Clt, Pno_Clt, Age_Clt, Wi_Clt, Tel_Clt, Mail_Clt, Adr_Clt, Mot_Clt, Sexe_Clt)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log("Erreur prepare inscription : " . mysqli_error($link));
    echo "<p style='font-size:20px; color:red;'>Erreur serveur. Veuillez réessayer.</p>";
    mysqli_close($link);
    exit();
}

mysqli_stmt_bind_param($stmt, 'ssissssss',
    $nom, $prenom, $age, $wilaya,
    $telephone, $email, $address,
    $hashedPassword, $sex
);

if (mysqli_stmt_execute($stmt)) {
    echo "<p style='font-size:20px; color:green;'>✅ Inscription réussie ! Vous pouvez maintenant vous connecter.</p>";
    echo "<script>setTimeout(() => window.location.href = 'login.html', 2500);</script>";
} else {
    error_log("Erreur inscription : " . mysqli_stmt_error($stmt));
    echo "<p style='font-size:20px; color:red;'>Erreur lors de l'inscription. Veuillez réessayer.</p>";
}

// ── Nettoyage ────────────────────────────────────────────────────────────────
mysqli_stmt_close($stmt);
mysqli_close($link);