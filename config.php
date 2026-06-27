<?php


define('DB_HOST', 'votre_host');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_NAME', 'votre_base_de_donnees');

function getConnection(): mysqli {
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$link) {
        error_log("Erreur de connexion : " . mysqli_connect_error());
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Erreur de connexion au serveur.']));
    }

    mysqli_set_charset($link, 'utf8mb4');
    return $link;
}