<?php
error_reporting(E_ALL & E_DEPRECATED & E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

// Inclure le fichier contenant la classe api
require_once('api.php');

// Instancier un objet de la classe api
$api = new api();

// Récupérer les données JSON envoyées via POST
$data = $_POST['functions'];


// Récupérer l'adresse IP de l'utilisateur
$adresse_ip = $_SERVER['REMOTE_ADDR'];

// Logguer les fonctions reçues dans un fichier de log avec l'adresse IP
$date = date('Y-m-d H:i:s');
/*$log_message = $date . ' - Adresse IP: ' . $adresse_ip . ' - Fonctions: ' . print_r($data, true) . PHP_EOL;
file_put_contents("fonctions.log", $log_message, FILE_APPEND);*/

$log_message = $date . ' - Adresse IP: ' . $adresse_ip . ' - Fonctions: ' . print_r($data, true) . PHP_EOL ;
file_put_contents("fonctions.log", $log_message, FILE_APPEND);

// Appeler la fonction getCustoms avec les fonctions définies
$resultat = $api->getCustoms($data);

// Convertir la chaîne JSON en un tableau PHP
$tableau_resultat = json_decode($resultat, true);

// Créer un nouveau tableau pour stocker les éléments sans les guillemets
$nouveau_tableau = [];

// Parcourir chaque élément du tableau
foreach ($tableau_resultat as $element) {
    // Décoder l'élément JSON et le réencoder sans les guillemets
    $decoded_element = json_decode($element, true);
    // Ajouter l'élément au nouveau tableau
    $nouveau_tableau[] = $decoded_element;
}

// Afficher le nouveau tableau encodé en JSON
echo json_encode($nouveau_tableau);

?>
