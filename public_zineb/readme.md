Outils utilisés dans les parties que j’ai réalisées.
<h1>Translation automatique: 👍</h1>

1. Connectez-vous avec votre compte Google sur ScriptApp (https://script.google.com/).

2. Créez un nouveau projet.

3. Créez un algorithme pour la traduction automatique.

```js
function doPost(e) {
  var error = null; 
  var translate = [];
  if (!e.parameter.source_lang) {
    error = "source_lang undefined";
  } else if (!e.parameter.target_lang) {
    error = "target_lang undefined";
  } else if (!e.parameter.text) {
    error = "text undefined";
  } else {
    var langues = e.parameter.target_lang.split(',').map(l => l.trim()); 
    var src_lang = e.parameter.source_lang.trim(); 
    var text = e.parameter.text.trim(); 

    if (langues.length === 1 && src_lang === langues[0]) {
      translate = [text];
    } else {
      try {
        langues.forEach((langue, index) => {
          translate[index] = LanguageApp.translate(text, src_lang, langue);
        });
      } catch (err) {
        error = err.message;
      }
    }
  }
  var result;
  if (!error) {
    result = JSON.stringify({
      status: 'success',
      translatedText: translate,
      target: langues,
    });
  } else {
    result = JSON.stringify({
      status: 'error',
      message: error,
      translate: translate,
      target: langues.length > 0 ? langues[0] : null,
    });
  }
  return ContentService.createTextOutput(result).setMimeType(ContentService.MimeType.JSON);
  }
```

4. Cliquez sur « Déployer » pour générer une URL. Copiez cette URL, puis utilisez fetch pour envoyer les langues source et cible ainsi que le texte à traduire en tant que requête.

<h1> SMTP (Simple Mail Transfer Protocol) : transférer un e-mail d’un client vers une boîte mail </h1>

1. Installez PHPMailer à l’aide de la commande :
    composer require phpmailer/phpmailer

2. Renseignez les informations nécessaires :
    Serveur SMTP
    Port
    Informations de l’expéditeur
    Informations du destinataire
    (Toutes les étapes sont expliquées dans le code.)
<h1>Abstract api :👍 vérifier l'existence d’email</h1>

1. Connectez-vous sur ce lien : https://app.abstractapi.com/

2. Créez une API, puis envoyez-lui une requête contenant une adresse e-mail. Elle vous renverra les informations associées à cet e-mail.
```js
   {"email":"[exemple1@gmail.com](mailto:exemple1@gmail.com)","autocorrect":"","deliverability":"UNDELIVERABLE","quality\_score":"0.00","is\_valid\_format":{"value":true,"text":"TRUE"} email non exist 
   } 
   {"email":"[exemple2@gmail.com](mailto:exemple2@gmail.com)","autocorrect":"","deliverability":"DELIVERABLE","quality\_score":"0.95","is\_valid\_format":{"value":true,"text":"TRUE"} email exist}
```

<<h1>Séparer les dossiers des images stockées dynamiquement du dossier racine : 👍</h1>

1. J’ai ajouté ceci dans le fichier .env :
    ```js
          # Définit la variable HOME_DIRECTORY en prenant le chemin vers le bureau de l'utilisateur actuel.
          # Elle sera utilisée pour référencer la racine du projet ou du répertoire public.
          # Pour les systèmes Linux, on utilise ${HOME} pour définir le répertoire racine de l'utilisateur.
          HOME_DIRECTORY=${HOME}/Bureau

          # Pour les systèmes Windows, on peut définir manuellement le chemin du répertoire, par exemple :
          # HOME_DIRECTORY=D:\MonProjet

          # Ligne commentée : alternative qui utiliserait le répertoire du projet Symfony directement.
          # %kernel.project_dir% est une variable spéciale de Symfony qui pointe vers la racine du projet.
          # HOME_DIRECTORY=%kernel.project_dir%
    ```
    2. j'ai modifie sur le fichier services.yaml:
      ```js
          # Répertoire contenant les dossiers des images (défini via une variable d'environnement HOME_DIRECTORY)
          project_dir: '%env(HOME_DIRECTORY)%'
          # Répertoire global contenant toutes les images
          images_directory: '%project_dir%/public/images/'
      ```
   3. J’ai ajouté une route pour afficher les images depuis un dossier externe :
   ```php
       #[Route('/images/{filename}/{directory}', name: 'afficher_image', requirements: ['filename' => '.+'])]
      public function image(string $filename, string $directory)
      {
          // Récupère le chemin de base du répertoire à partir des paramètres définis dans services.yaml
          $baseDir = $this->getParameter($directory);

          // Construit le chemin absolu de l'image
          $fullPath = realpath($baseDir . '/' . $filename);

          // Vérifie si le fichier existe et qu'il est bien situé dans le répertoire autorisé
          if (!$fullPath) {
            // Si le fichier est introuvable ou en dehors du répertoire, une erreur 404 est lancée
            throw $this->createNotFoundException('Image not found.');
          }

          // Retourne la réponse contenant l'image en mode inline (affichée dans le navigateur)
          return new BinaryFileResponse($fullPath, 200, [
            'Content-Disposition' => ResponseHeaderBag::DISPOSITION_INLINE
        ]);
      }
   ```
<h1>API pour afficher la liste des villes afin de sélectionner la ville exacte :</h1> 
J'ai fait un appel à l'URL suivante :
   ```js
         const url = `https://nominatim.openstreetmap.org/search?city=${text}&format=json`;
   ```
Cette API permet d'obtenir une liste de villes à partir d'un préfixe saisi.
