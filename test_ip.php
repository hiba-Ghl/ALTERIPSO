<?php
// Decode instead of extract, look at the output
	$CONFIG_STR = file_get_contents('C:/wamp/www/site_config.php');
    // actually, let's just use the values since it printed them:
    $db = new PDO("mysql:charset=utf8;mysql:host=localhost;dbname=rsmartv", 'root', 'root');
    $req = $db->query("SELECT c.etablissement_id as id, c.ip FROM chambre c INNER JOIN annonce_chambre ac ON c.id = ac.chambre_id LIMIT 1");
    print_r($req->fetch(PDO::FETCH_ASSOC));
?>