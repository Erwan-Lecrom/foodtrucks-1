<?php
include_once '../private/app/init-ajax.php';
// On accepte les requetes POST uniquement
if ($_SERVER['REQUEST_METHOD'] == "POST") {


  var_dump($_POST);

  // Récupération des données du $_POST
$pwd_old= isset($_POST['pwd_old']) ? $_POST['pwd_old'] : null;
$pwd_new= isset($_POST['pwd_new']) ? $_POST['pwd_new'] : null;
$pwd_repeat= isset($_POST['pwd_repeat']) ? $_POST['pwd_repeat'] : null;

  // Controle des données

  // - Controle du mot de passe actuel
  // --
  // -> le mot de passe ne doit pas être vide
  if ($pwd_old==null){
    save=false;
  }
  // -> le mot de passe doit correspondre avec celui deja enregistré dans la BDD
  else if ($_SESSION["user"]["password"]!=$pwd_old){
    save=false;
  }
  // - Controle du nouveau mot de passe
  // --
  if (strlen($pwd_new)<8) {
      // -> doit contenir au moins 8 caractères
    save=false;
  }else if (strlen($pwd_new)>16) {
      // -> doit contenir au plus 16 caractères
    save=false;
  }else if (!preg_match("/[0-9]/",$pwd_new){
    // -> doit avoir au moins un caractère de type numérique
    save=false;
  }else if (!preg_match("/[A-Z]/",$pwd_new){
    // -> doit avoir au moins un caractère en majuscule
    save=false;
  } else if (!preg_match("/(#|@|!|=|\+|-|_)/",$pwd_new){
    // -> doit avoir au moins un caractère spécial (#@!=+-_)
    save=false;
  }
  // - Controle de la répétition du nouveau mot de passe
  if ($pwd_new!=$pwd_repeat){
    save=false;
  }
  // --
  // -> Les mots de passe doivent être identique

  if ($save) {

    // Cryptage du nouveau mot de passe
    $pwd_new = password_hash($pwd_new, PASSWORD_DEFAULT);
    // Requete de modification du mot de passe dans la BDD
    $q = $pdo->query("UPDATE $database SET password=$pwd_new WHERE email=$_POST");
    // Definition du message de réponse + encodage de la reponse en JSON
    setFlashBag("success","Le mot de passe a bien été remplacé");
  }

}

// Si les requetes ne sont pas en POST, on refuse l'accès.
else {
  echo "Vous n'avez pas l'autorisation d'afficher ce fichier.";
}
