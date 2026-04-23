<?php
/**
 * An immaginative class. You can immagine a DB interface instead or what you want
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 */
 

error_reporting(E_ALL & E_DEPRECATED & E_NOTICE);
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
class api {



        private $db;
        public function __Construct(){
	
	// ob_start();
	require 'C:/wamp/www/site_config.php';
	// ob_end_clean();
	
	// Si le tableau $CONFIG existe on l'extrait
	if (isset($CONFIG) && is_array($CONFIG)) {
	    extract($CONFIG); // crée les variables automatiquement
    }

			
			$this->db = new PDO("mysql:charset=utf8mb4;mysql:host=$ServerHostRsmartv;dbname=$DatabaseNameRsmartv", $DatabaseUserRsmartv, $DatabasePassRsmartv);
			//$this->conn2 = new PDO("mysql:host=$ServerHostPaytv;dbname=$DatabaseNamePaytv", $DatabaseUserPaytv, $DatabasePassPaytv);
			
        }

	

				
	function getCustoms($functions)
{
    $results = array();
    $functions_array = json_decode($functions, true);
    foreach ($functions_array as $function) {
        $post_data = null;
        switch ($function["function"]) {
            case 'getjsoncat':
                $id = $function["params"]["id"];

                // Utilisation d'une requête préparée pour éviter les attaques par injection SQL
                $req = $this->db->prepare("SELECT * FROM `categories` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);

                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }

                // Conversion du tableau en JSON avec JSON_FORCE_OBJECT
                $post_data = json_encode(["getjsoncat" => $post_data]);
                break;

            case 'getJsonIPTVChannels':
                $id = $function["params"]["id"];

                // Utilisation d'une requête préparée pour éviter les attaques par injection SQL
                $req = $this->db->prepare("SELECT * FROM `television` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);

                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }

                // Conversion du tableau en JSON avec JSON_FORCE_OBJECT
                $post_data = json_encode(["getJsonIPTVChannels" => $post_data]);
                break;
            case 'getjsonetablissement':
                $id = $function["params"]["id"];

                // Utilisation d'une requête préparée pour éviter les attaques par injection SQL
                $req = $this->db->prepare("SELECT * FROM `etablissement` WHERE `id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);

                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }

                // Conversion du tableau en JSON avec JSON_FORCE_OBJECT
                $post_data = json_encode(["getjsonetablissement" => $post_data]);
                break;
            case 'getBoxMac':
                $mac = $function["params"]["mac"];
                $req = $this->db->query("SELECT * FROM `chambre` WHERE mac like '" . $mac . "'");
                $post_data1 = array();
                $post_data1 = $req->fetch(PDO::FETCH_ASSOC);
                $id = $post_data1['etablissement_id'];
                $req = $this->db->query("SELECT *  FROM `etablissement` WHERE id like '" . $id . "'");
                $post_data2 = array();
                $post_data2 = $req->fetch(PDO::FETCH_ASSOC);
                $post_datas = array();
                $post_datas = array_merge($post_data, $post_data2);
                if (empty($post_datas)) {
                    $post_datas = array('reponse' => 'false');
                }
                $post_datas = json_encode($post_datas, JSON_FORCE_OBJECT);
                $post_data = json_encode(["getBoxMac" => $post_datas]);
                break;
			case 'getcheck':
				$ip = $function["params"]["ip"];
				$id = $function["params"]["id"];
				$req = $this->db->query("SELECT * FROM `chambre` WHERE etablissement_id = ".$id." and ip = '" . $ip . "'");
				$post_data1 = $req->fetch(PDO::FETCH_ASSOC);

				// Vérifier si $post_data1 contient un résultat
				if ($post_data1 !== false) {
					//$id = $post_data1['etablissement_id'];
					$req = $this->db->query("SELECT access_tv_in_checkout,msgbienvenu FROM `etablissement` WHERE id like '" . $id . "'");
					$post_data2 = $req->fetch(PDO::FETCH_ASSOC);
					
					// Utiliser les bonnes variables dans array_merge()
					$post_datas = array_merge($post_data1, $post_data2);

					if (empty($post_data1)) {
						$post_datas = array('reponse' => 'false');
					}
				} else {
					// Si $post_data1 est vide, définir $post_datas comme 'false'
					$post_datas = array('reponse' => 'false');
				}

				// Convertir $post_datas en JSON sans les caractères d'échappement "\"
				$post_data = json_encode(["getcheck" => $post_datas], JSON_UNESCAPED_SLASHES);
				break;     
            case 'getCatRadio':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `categorie_radio` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getCatRadio" => $post_data]);
                break;
            case 'getRadio':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `radio` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getRadio" => $post_data]);
                break;
			case 'getCatLivreAudio':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `categorie_livreaudio` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getCatLivreAudio" => $post_data]);
                break;
            case 'getLivreAudio':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `livreaudio` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getLivreAudio" => $post_data]);
                break;
            case 'getJsonApplication':
                $id = $function["params"]["id"];
                $roomtype = isset($function["params"]['roomtype']) ? $function["params"]['roomtype'] : null; // Vérifie si la clé roomtype existe
                $sql = "SELECT * FROM `application` WHERE active = '1' AND `etablissement_id` = ?";
                $params = [$id];
                if (!is_null($roomtype)) {
                    $sql .= " AND protocole = ?";
                    $params[] = $roomtype;
                } else {
                    $sql .= " AND protocole = 1"; // Ajouter la condition protocole = 1 lorsque roomtype est null
                }
                $req = $this->db->prepare($sql);
                $req->execute($params);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getJsonApplication" => $post_data]);
                break;
			case 'getJsonJeux':
                $id = $function["params"]["id"];
                $roomtype = isset($function["params"]['roomtype']) ? $function["params"]['roomtype'] : null; // Vérifie si la clé roomtype existe
                $sql = "SELECT * FROM `jeux` WHERE active = '1' AND `etablissement_id` = ?";
                $params = [$id];
                if (!is_null($roomtype)) {
                    $sql .= " AND protocole = ?";
                    $params[] = $roomtype;
                } else {
                    $sql .= " AND protocole = 1"; // Ajouter la condition protocole = 1 lorsque roomtype est null
                }
                $req = $this->db->prepare($sql);
                $req->execute($params);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getJsonJeux" => $post_data]);
                break;

			case 'getQuestionnaire':
                $id = $function["params"]["id"];
                $service = isset($function["params"]['service']) ? $function["params"]['service'] : null; // Vérifie si la clé service existe
                $sql = "SELECT * FROM `questionnaire` WHERE active = '1' AND `etablissement_id` = ?";
                $params = [$id];
                if (!is_null($service)) {
                    $sql .= " AND service_id = ?";
                    $params[] = $service;
                } else {
                    $sql .= " AND service_id = 1"; // Ajouter la condition service general = 1 lorsque roomtype est null
                }
                $req = $this->db->prepare($sql);
                $req->execute($params);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getQuestionnaire" => $post_data]);
                break;

			case 'SetAnswer':
				$ip = $function["params"]["ip"];
				$id = $function["params"]["id"];
				$tab = $function["params"]["tab"];

				// Ajouter un log pour vérifier les paramètres
				//error_log("SetAnswer params - ip: $ip, id: $id, tab: " . json_encode($tab));

				try {
					// Vérifiez la connexion à la base de données
					$this->db->query("SELECT 1");
					//error_log("Database connection is working.");

					// Récupérer les champs id, service_id et checkval de la table `chambre`
					$req = $this->db->prepare("SELECT id, service_id, checkval FROM `chambre` WHERE ip = ? AND `etablissement_id` = ?");
					$req->execute([$ip, $id]);
					$results = $req->fetch(PDO::FETCH_ASSOC);

					// Ajouter un log pour vérifier les résultats de la requête
					//error_log("SetAnswer query results: " . json_encode($results));

					if ($results) {
						$chambre_id = $results['id'];
						$service_id = $results['service_id'];

						// Vérifiez si $tab est une chaîne JSON ou un tableau
						if (is_string($tab)) {
							// Décoder le paramètre `tab`
							$decoded_tab = json_decode($tab, true);

							// Vérifiez les erreurs de JSON
							if (json_last_error() === JSON_ERROR_NONE) {
								$tab = $decoded_tab; // Utilisez le tableau décodé
							} else {
								// Log l'erreur de JSON
								//error_log("JSON decode error: " . json_last_error_msg());
								$results = ['reponse' => 'false'];
								$post_data = json_encode(["setAnswer" => $results], JSON_FORCE_OBJECT);
								break;
							}
						} elseif (is_array($tab)) {
							// Si $tab est déjà un tableau, pas besoin de décoder
							$decoded_tab = $tab;
						} else {
							// $tab n'est ni une chaîne JSON ni un tableau
							//error_log("Tab is not a valid JSON string or array.");
							$results = ['reponse' => 'false'];
							$post_data = json_encode(["setAnswer" => $results], JSON_FORCE_OBJECT);
							break;
						}

						// Préparer l'insertion des résultats dans la table `resultat_questionnaire`
						$insert_query = $this->db->prepare("INSERT INTO `resultat_questionnaire` (`etablissement_id`, `questionnaire_id`, `chambre_id`, `service_id`, `vote`, `date`) VALUES (?, ?, ?, ?, ?, ?)");

						// Insérer les valeurs pour chaque questionnaire_id et vote
						foreach ($decoded_tab as $questionnaire_id => $vote) {
							// Ajouter un log avant l'exécution de la requête d'insertion
							//error_log("Inserting - etablissement_id: $id, questionnaire_id: $questionnaire_id, chambre_id: $chambre_id, service_id: $service_id, vote: $vote");

							$success = $insert_query->execute([$id, $questionnaire_id, $chambre_id, $service_id, $vote, date('Y-m-d H:i:s')]);

							// Ajouter un log après l'exécution de la requête d'insertion
							if ($success) {
								//error_log("Insertion successful for questionnaire_id: $questionnaire_id");
							} else {
								// Ajouter un log si l'insertion échoue
								$errorInfo = $insert_query->errorInfo();
								//error_log("Insertion failed for questionnaire_id: $questionnaire_id. Error: " . json_encode($errorInfo));
							}
						}

						// Préparer les données de réponse
						$results = ['reponse' => 'true'];
					} else {
						// Ajouter un log si aucun résultat n'est trouvé
						//error_log("No results found for ip: $ip and id: $id");
						$results = ['reponse' => 'false'];
					}

					$post_data = json_encode(["setAnswer" => $results], JSON_FORCE_OBJECT);
				} catch (PDOException $e) {
					// Log PDO exception
					//error_log("PDOException: " . $e->getMessage());
					$post_data = json_encode(["setAnswer" => ['reponse' => 'false']]);
				}
				break;
			case 'getService':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `services` WHERE active = '1'  AND `etablissement_id` = ? ORDER BY position");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getService" => $post_data]);
                break;
			case 'getAnnonce':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `annonce` WHERE `etablissement_id` = ?");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getAnnonce" => $post_data], JSON_UNESCAPED_UNICODE);
                break;
			/*case 'getService2':
                $id = $function["params"]["id"];
                $req = $this->db->prepare("SELECT * FROM `services` WHERE active = '1' AND service = 2 AND `etablissement_id` = ? ORDER BY position");
                $req->execute([$id]);
                $post_data = $req->fetchAll(PDO::FETCH_ASSOC);
                if (empty($post_data)) {
                    $post_data = ['reponse' => 'false'];
                }
                $post_data = json_encode(["getService2" => $post_data]);
                break;*/
			
















            default:
                $post_data = null; // Retourne null si la fonction n'est pas reconnue
                break;
        }
        if ($post_data != null) {
            array_push($results, $post_data);
        }
    }
    return json_encode($results);
}

  
	



}
?>