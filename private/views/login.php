<?php

//Cas ou l'utilisateur envoie le formulaire
//controle du formulaire
if ($_SERVER['REQUEST_METHOD']=='POST'){
	$save=true;
	// Recuperer les données donnnées par le formulaire
	$token = isset($_POST['token']) ? $_POST['token'] : null;
	$login = isset($_POST['login']) ? $_POST['login'] : null;
	$password = isset($_POST['password']) ? $_POST['password'] : null;

	//controler l'integrité du token
	if (!isset($_SESSION['token']) || empty($_SESSION['token']) || $_SESSION['token'] !==$token ){
		$save=false;
		setFlashbag("danger","le token est invalide");
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

	// Controle l'existance de l'utilisateur dans la BDD
	// Recup id, firstname, lastname, email, password
	// On effectue la requete uniquement sur le champ email (wher email=$login)
	if ($save){
		if (!($user=getUserByEmail($login))){
			$save=false;
			setFlashbag("danger","L'utilisateur $login n'a pas ete trouvé dans la base de données");
		}
	}


	// Controle du MDP
	if ($save && $user) {

			// MDP crypté, récupéré depuis la BDD
			$pwd_hash=$user->password;
			// MDP en clair récupéré depuis le formulaire de connexion
			$pwd_text=$_POST['password'];
			// On controle le HASH des deux MDP
			if (!password_verify($pwd_text,$pwd_hash)){
				$save=false;
				setFlashbag("danger","Erreur d'Identification");
			}
			// on log l'utilisateur si tout s'est ibne passé
			if ($save){
				//identification de l'utilisateur
				$_SESSION['user']=[
					"id"=>$user->id,
					"firstname"=>$user->firstname,
					"lastname"=>$user->lastname,
					"email"=>$user->email,
					"roles"=>explode(ROLES_GLUE,$user->roles)
				];
				//Destruction du token
				unset($_SESSION['token']);
				//Redirection vers sa poage de profil
				header ("location: index.php?page=profile");
				exit;
			}
		}else {
		$_SESSION['token']=getToken();
	}
}
?>
<div class="page-header">
    <h2>Connexion</h2>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4">

        <?php getFlashbag(); ?>

        <form method="post">

            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

            <div class="form-group">
                <label for="login">Identifiant (adresse email)</label>
                <input  class="form-control" type="text" id="login" name="login">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input  class="form-control" type="password" id="password" name="password">
            </div>

            <br>
            <button type="submit" class="btn btn-info btn-block">Valider</button>
        </form>

        <p class="text-center">
            <a href="index.php?page=register">Je n'ai pas encore de compte</a>
        </p>

    </div>
</div>
