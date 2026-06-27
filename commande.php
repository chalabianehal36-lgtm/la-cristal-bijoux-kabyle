<?php


session_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'config.php';

// ── Vérification de la session (utilisateur connecté) ────────────────────────
if (!isset($_SESSION['id'])) {
    echo "<p style='font-size:20px; color:red;'>
            ⚠ Vous devez être connecté pour passer une commande.
            <a href='login.html'>Se connecter</a>
          </p>";
    exit();
}

// ── Vérification de la méthode HTTP ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

// ── Récupération et nettoyage des données ────────────────────────────────────
$vendeur  = trim($_POST['Vendeur']   ?? '');
$price    = trim($_POST['num']       ?? '');
$ref      = trim($_POST['reference'] ?? '');
$quantite = intval($_POST['quantite'] ?? 0);
$clientId = intval($_SESSION['id']);

// ── Validation ────────────────────────────────────────────────────────────────
$errors = [];

if (empty($vendeur))
    $errors[] = "Vendeur manquant.";

if (empty($price))
    $errors[] = "Prix manquant.";

if (empty($ref))
    $errors[] = "Référence manquante.";

if ($quantite < 1 || $quantite > 1000)
    $errors[] = "Quantité invalide (entre 1 et 1000).";

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<p style='font-size:16px; color:red;'>⚠ $err</p>";
    }
    exit();
}

// ── Connexion à la base de données ───────────────────────────────────────────
$link = getConnection();

// ── Insertion avec requête préparée ──────────────────────────────────────────
$stmt = mysqli_prepare($link,
    "INSERT INTO commande_produit (Id_client, Vendeur_prod, Prix_prod, Ref_prod, Qant_prod)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log("Erreur prepare commande : " . mysqli_error($link));
    echo "<p style='font-size:20px; color:red;'>Erreur serveur. Veuillez réessayer.</p>";
    mysqli_close($link);
    exit();
}

mysqli_stmt_bind_param($stmt, 'isssi',
    $clientId, $vendeur, $price, $ref, $quantite
);

if (mysqli_stmt_execute($stmt)) {
    $orderId = mysqli_insert_id($link);
    echo "
    <div style='font-family:Segoe UI,sans-serif; text-align:center; margin-top:40px;'>
        <p style='font-size:22px; color:green;'>
            ✅ Commande <strong>#$orderId</strong> enregistrée avec succès !
        </p>
        <p style='font-size:16px; color:#555;'>
            Produit : <strong>$vendeur</strong> | Réf : <strong>$ref</strong> | Quantité : <strong>$quantite</strong>
        </p>
        <a href='index.html' style='
            display:inline-block; margin-top:15px;
            padding:10px 20px; background:#d4a373;
            color:white; border-radius:20px;
            text-decoration:none; font-weight:bold;'>
            ← Retour au catalogue
        </a>
    </div>";
} else {
    error_log("Erreur commande : " . mysqli_stmt_error($stmt));
    echo "<p style='font-size:20px; color:red;'>Erreur lors de l'enregistrement. Veuillez réessayer.</p>";
}

// ── Nettoyage ────────────────────────────────────────────────────────────────
mysqli_stmt_close($stmt);
mysqli_close($link);
