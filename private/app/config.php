<?php

// --------------------
// PARAMETRES DE CONNECTION BDD
// --------------------

// Adresse du serveur de base de données
// "localhost" est un alias de 127.0.0.1
$host = "127.0.0.1";

// Nom d'utilisateur de la base de données
$user = "root";

// Mot de passe associé à l'utilisateur (root) de la base de données
$pass = "";

// Nom de la base de données sur laquelle on va travailler
$database = "foodtrucks";



// --------------------
// CONSTANTES
// --------------------

// -- Execution mode
define("MODE", "dev"); // dev | prod

// -- Directories
define("VIEWS_DIRECTORY", "../private/views/");
define("FUNCTIONS_DIRECTORY","../private/functions/");
define("FUNCTIONS_FILES","/php$/");
define("MODELS_DIRECTORY","../private/models/");
define("MODELS_FILES","../private/functions/");


// --------------------
// DEFAULT VARS
// --------------------

$default_page = "home";
