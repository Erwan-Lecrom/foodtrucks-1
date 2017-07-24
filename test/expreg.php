<?php
$scandir = array(
  ".",
  "..",
  "fichier.php",
  "dossier",
  "index.html",
  "php.js",
   "a.php",
  "aa.php",
  "aaa.php",
  "aaaa.php",
  "aa9aa.php",
  "0123456789"
);
// expression reguliere ("/expression reguliere/FLAG") FLAG (i=insensitive case,m=multiline,g=globale)
// . =tous les caractere , * = n'importe le nombre de caractere 
$expreg="/^php/";//^ = commence par 
$expreg="/php$/";//$ = se termine par
$expreg="/[0-9]/";//[0,9] = possedent un numerique
$expreg="/b{2}/"; // b{2} = contient 2 b consecutifs

foreach ($scandir as $value) {
  if ( preg_match($expreg, $value) ) {
    echo $value."<br>";
  }
}