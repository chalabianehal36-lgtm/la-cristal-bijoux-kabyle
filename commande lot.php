<?php
/**
 * commande_lot.php — Enregistrement de tout le panier en une seule requête
 * La Cristal des Bijoux Kabyle
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

// ── Authentification ──────────────────────────────────────────────────────────
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté.']);
    exit();
}

// ── Méthode HTTP ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit();
}

// ── Lecture du JSON ───────────────────────────────────────────────────────────
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!isset($data['articles']) || !is_array($data['articles']) || count($data['articles']) === 0) {
    echo json_encode(['success' => false, 'message' => 'Panier vide ou données invalides.']);
    exit();
}

$clientId = intval($_SESSION['id']);
$articles = $data['articles'];
$errors   = [];

// ── Validation de chaque article ──────────────────────────────────────────────
foreach ($articles as $i => $art) {
    $vendeur  = trim($art['name']     ?? '');
    $price    = trim($art['price']    ?? '');
    $ref      = trim($art['ref']      ?? '');
    $quantite = intval($art['quantite'] ?? 0);

    if (empty($vendeur) || empty($price) || empty($ref)) {
        $errors[] = "Article #" . ($i + 1) . " : données incomplètes.";
    }
    if ($quantite < 1 || $quantite > 999) {
        $errors[] = "Article #" . ($i + 1) . " : quantité invalide.";
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit();
}

// ── Connexion et insertion en lot ─────────────────────────────────────────────
$link = getConnection();

$stmt = mysqli_prepare($link,
    "INSERT INTO commande_produit (Id_client, Vendeur_prod, Prix_prod, Ref_prod, Qant_prod)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log("Erreur prepare lot : " . mysqli_error($link));
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
    mysqli_close($link);
    exit();
}

mysqli_begin_transaction($link);

$insertedIds = [];
$success = true;

foreach ($articles as $art) {
    $vendeur  = trim($art['name']     ?? '');
    $price    = trim($art['price']    ?? '');
    $ref      = trim($art['ref']      ?? '');
    $quantite = intval($art['quantite'] ?? 0);

    mysqli_stmt_bind_param($stmt, 'isssi',
        $clientId, $vendeur, $price, $ref, $quantite
    );

    if (!mysqli_stmt_execute($stmt)) {
        error_log("Erreur lot : " . mysqli_stmt_error($stmt));
        $success = false;
        break;
    }
    $insertedIds[] = mysqli_insert_id($link);
}

if ($success) {
    mysqli_commit($link);
    echo json_encode([
        'success'  => true,
        'message'  => 'Commande enregistrée avec succès !',
        'order_ids' => $insertedIds
    ]);
} else {
    mysqli_rollback($link);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement.']);
}

mysqli_stmt_close($stmt);
mysqli_close($link);