<?php

// Definition des variables par défaut

$month=["janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"];

// Cas où l'utilisateur envoie le formulaire (méthode POST)
// Contrôle du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $save=true;
    $login=null;
    $password=null;
    $token=null;
    $firstname=null;
    $lastname=null;
    $gender=null;
    $birth_day=null;
    $birth_month=null;
    $birth_year=null;

    // Recupérer les données de $_POST
    $token = isset($_POST['token']) ? $_POST['token'] : null;
    $login = isset($_POST['login']) ? $_POST['login'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $firstname   = isset($_POST['firstname']) ? $_POST['firstname'] : null;
    $lastname    = isset($_POST['lastname']) ? $_POST['lastname'] : null;
    $gender      = isset($_POST['gender']) ? $_POST['gender'] : null;
    $birth_day  = isset($_POST['birth']['day']) ? $_POST['birth']['day'] : null;
    $birth_month= isset($_POST['birth']['month']) ? $_POST['birth']['month'] : null;
    $birth_year = isset($_POST['birth']['year']) ? $_POST['birth']['year'] : null;

    // Controler l'intégrité du token
    if ($_SESSION['token']!== $token){
        $save = false ;
        setFlashBag("danger","Le token est invalide");
    }
    // - Controle de l'adresse email
    // --
    // -> ne doit pas etre vide
    if (empty($login)){
        $save=false;
        setFlashBag("danger","veuillez saisir un identifiant");
    }
    // -> doit avoir la syntaxe d'une adresse email valide
    elseif (!filter_var($login,FILTER_VALIDATE_EMAIL)){
        $save = false;
        setFlashBag("danger","Veuillez saisir une adresse email valide");
    }
    // - Controle du mot de passe
    // --
    // -> doit contenir au moins 8 caractères
    if (strlen($password) < 8 || strlen($password) > 16) {
        $save=false;
        setFlashBag("danger","le mot de passe doit contenir 8 caracteres minimum et 16 caracteres maximum");
    }
    // -> doit contenir au plus 16 caractères
    // -> doit avoir au moins un caractère de type numérique
    elseif (!preg_match("/[0-9]/",$password)){
        $save=false;
        setFlashBag("danger","le mot de passe doit au moins un caractere numerique");
    }
    // -> doit avoir au moins un caractère en majuscule
    elseif (!preg_match("/[A-Z]/", $password)){
        $save=false;
        setFlashBag("danger","Le mot de passe doit contenir au moins une majuscule");
    }
    // -> doit avoir au moins un caractère spéciale (#@!=+-_)
    elseif (!preg_match("/(#|@|!|=|\+|-|_)/", $password) ) {
        $save=false;
        setFlashBag("danger","Le mot de passe  doit contenir au moins un caractere speciale (#@!=+-_)");
    }
    // On Crypte le Mot de passe
    else {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }
    // - Controle du prénom
    // --
    // -> doit être une chaine alphabetique
    // -> peut contenir un tiret
    // -> ne doit pas possèder de caractère numérique
    if (preg_match("/^[0-9][a-z-]*[a-z]$/i", $firstname)){
        $save=false;
        setFlashBag("danger","le prenom ne doit pas contenir de caractere numerique");
    }
    // - Controle du Nom de famille
    // --
    // -> doit être une chaine alphabetique
    // -> peut contenir un tiret
    // -> ne doit pas possèder de caractère numérique
     if (preg_match("/^[0-9][a-z-]*[a-z]$/i", $lastname)){
        $save=false;
        setFlashBag("danger","le nom de famille ne doit pas contenir de caractere numerique");
    }

    // - Controle de la date de naissance
    // --
    if (empty($birth_day)){
        $save=false;
        setFlashBag("danger","Veuilez saisir un jour de naissance");
    }
    elseif (empty($birth_month)){
        $save=false;
        setFlashBag("danger","veuillez saisir un mois de naissance");
    }
    elseif (empty($birth_year)){
        $save=false;
        setFlashBag("danger","veuillez saisir une année de naissance");
    }
    // -> doit etre une date valide
    if ($birth_day!=null || $birth_month!=null || $birth_year!=null){
        if (!checkdate($birth_month, $birth_day, $birth_year)){
            $save=false;
            setFlashBag("danger","veuillez saisir une date valide");
        }else{
            $birthday = $birth_year."-".$birth_month."-".$birth_day;
        }
    }
    // -> doit être supérieur à 13ans au moment de l'inscription
      if (isset($birthday)) {
          $tz  = new DateTimeZone('Europe/Brussels');
          $age = DateTime::createFromFormat('Y-m-d', $birthday, $tz)
                 ->diff(new DateTime('now', $tz))
                 ->y;
          $minAge = 13;
          if ($age < $minAge) {
              $save = false;
              setFlashbag("danger", "Vous devez avoir au moins $minAge ans pou vous inscrire.");
          }
        }
    // - Controle le genre
    // --
    if ($gender===null){
        $save=false;
        setFlashbag("danger","Veuillez selectionner un genre");
    }
    // -> Le champ doit possèder une valeur (M ou F)
    }elseif ($gender!="M" && $gender!="F"){
        $save=false;
        setFlashBag("danger","Le sexe doit etre feminin ou masculin");
    }

    // - Controle des condition d'utilisation du service
    // --
    // -> La checkbox doit etre cochée.
    if (!isset($_POST['acceptTerms'])) {
        $save=false;
        setFlashBag("danger","Les conditions d'utilisation doivent etre acceptes");
    }

    // - Controle l'existance de l'utilisateur dans la BDD
    // -> L'adresse email ne doit pas etre présente dans la BDD (table users)
    // On enregistre l'utilisateur dans la BDD
    if (userExists($login)){
        $save=false;
        setFlashbag("danger","l'email est deja utilisé");
    }
    if ($save) {

        // Enregistre l'utilisateur

        $iduser=addUser(array(
            "firstname"=>$firstname,
            "lastname"=>$lastname,
            "login"=>$login,
            "password"=>$password,
            "gender"=>$gender,
            "birthday"=>$birthday));
        // Identification de l'utilisateur
        $_SESSION['user']=[
            "id"=>$iduser,
            "firstname"=>$firstname,
            "lastname"=>$lastname,
            "email"=>$login,
            "roles"=>$default_users_roles,
        ];
        setFlashbag("success","Bienvenue $firstname");
        // Destruction du token
        unset($_SESSION['token']);

        // Redirection de l'utilisateur
        header("location: index.php?page=profile");
        exit;

    }

// Cas où l'utilisateur arrive sur la page sans envoyer le formulaire (méthode GET)
else {
    // Definition du token
    $_SESSION['token'] = getToken();
}
?>

<div class="page-header">
    <h2>Inscription</h2>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4">

        <?php getFlashbag(); ?>

        <form method="post">

            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

            <div class="form-group">
                <label for="login">Identifiant (adresse email)</label>
                <input  class="form-control" type="text" id="login" name="login" value="<?php echo $login; ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input  class="form-control" type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input  class="form-control" type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>">
            </div>

            <div class="form-group">
                <label for="lastname">Nom de famille</label>
                <input  class="form-control" type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>">
            </div>

            <div class="form-group">
                <label>Genre</label>
                <label><input type="radio" name="gender" value="F" <?php echo $gender == "F" ? "checked" : null; ?>> Féminin </label>
                <label><input type="radio" name="gender" value="M" <?php echo $gender == "M" ? "checked" : null; ?>> Masculin </label>
                <label><input type="radio" name="gender" value="T" <?php echo $gender == "T" ? "checked" : null; ?>> Trans </label>
                <label><input type="radio" name="gender" value="A" <?php echo $gender == "A" ? "checked" : null; ?>> Alien </label>
            </div>

            <div class="form-group">
                <label for="birthday">Date de naissance</label>
                <div class="row">
                    <div class="col-md-4">
                        <select  class="form-control" id="birthday" name="birth[day]">
                            <option value="">Jour</option>
                            <?php for($i=1; $i<=31; $i++): ?>
                                <option value="<?php echo str_pad($i, 2, 0, STR_PAD_LEFT); ?>"><?php
                                    echo str_pad($i, 2, 0, STR_PAD_LEFT);
                                ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select  class="form-control" name="birth[month]">
                            <option value="">Mois</option>
                            <?php for($i=0; $i<12; $i++): ?>
                                <option value="<?php echo str_pad(($i+1), 2, 0, STR_PAD_LEFT); ?>"><?php echo $month[$i]; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select  class="form-control" name="birth[year]">
                            <option value="">Années</option>
                            <?php for($i=date('Y'); $i>date('Y')-100; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label>
                    <input type="checkbox" name="acceptTerms">
                    J'accepte les conditions d'utilisation du service.
                </label>

            </div>

            <br>
            <button type="submit" class="btn btn-info btn-block">Valider</button>
        </form>

        <p class="text-center">
            <a href="index.php?page=login">J'ai déjà compte</a>
        </p>


    </div>
</div>
