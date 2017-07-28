"use strict";
$(document).ready(function(){
  //Afficher / Masquez le mot de passe
  $('.input-group-addon').click(function(){
    toggleViewPwd($(this));
    $(this).find("i")
  });
  // Envoie du formulaire
  $('#btn-send').click(function(){
    sendrenewpwd();
  });

});
function toggleViewPwd(fieldid){
  var field= $('#'+fieldid.data('toggleView'));
  if(field.attr('type')=="password"){
      field.attr('type','text');

  }else if (field.attr('type')=="text"){
    field.attr('type','password');
  }
  fieldid.find('i')
     .toggleClass('glyphicon-eye-open')
     .toggleClass('glyphicon-eye-close');
}
function sendrenewpwd(){
  //Suppression des messages d'Erreur
  $('.text-danger').remove();

  //recuperation des données du formulaire
    var pwd_old =$('#pwd_old').val();
    var pwd_new =$('#pwd_new').val();
    var pwd_repeat =$('#pwd_repeat').val();

    /*var values={
      "pwd_old":pwd_old,
      "pwd_new":pwd_new,
      "pwd_repeat":pwd_repeat
    };
    console.log(values);
    console.log(pwd_old, pwd_new, pwd_repeat);

    console.log($('form').serializeArray());*/
    var send=true;
    if (pwd_old.length<=0){
      send=false;
      $('#pwd_old').before('<div class="text-danger">Le champ ne doit pas etre vide</div>');
    }
    // - Controle du mot de passe actuel
   // --
   // -> le mot de passe ne doit pas être vide
   if (empty($pwd_old)) {
     $save = false;
   }
   // -> le mot de passe doit correspondre avec celui deja enregistré dans la BDD
   // - Controle du nouveau mot de passe
   // --
   // -> doit contenir au moins 8 caractères
   // -> doit contenir au plus 16 caractères
   if (strlen($pwd_new) < 8 || strlen($pwd_new) > 16) {
       $save = false;
       // setFlashbag("danger", "Le mot de passe doit avoir 8 caractères minimum et 16 caractères maximum.");
       echo "{\"state\":\"danger\", \"message\":\"Le mot de passe doit avoir 8 caractères minimum et 16 caractères maximum.\"}";
   }
   // -> doit avoir au moins un caractère de type numérique
   elseif (!preg_match("/[0-9]/", $pwd_new)) {
       $save = false;
       // setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère numérique.");
       echo '{"state":"danger", "message":"Le mot de passe doit contenir au moins un caractère numérique."}';
   }
   // elseif (strlen(filter_var($password, FILTER_SANITIZE_NUMBER_INT)) <= 0) {
   //     $send = false;
   //     setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère numérique."};
   // }
   // -> doit avoir au moins un caractère en majuscule
   elseif (!preg_match("/[A-Z]/", $pwd_new)) {
       $save = false;
       // setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère en majuscule.");
       echo json_encode(array(
         "state" => "danger",
         "message" => "Le mot de passe doit contenir au moins un caractère en majuscule."
       ));
   }
   // -> doit avoir au moins un caractère spécial (#@!=+-_)
   elseif (!preg_match("/(#|@|!|=|\+|-|_)/", $pwd_new)) {
       $save = false;
       // setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère spécial (#@!=+-_).");
       echo json_encode(array(
         "state" => "danger",
         "message" => "Le mot de passe doit contenir au moins un caractère spécial (#@!=+-_)."
       ));
   }
    // - Controle de la répétition du nouveau mot de passe
    if (pwd_new!=pwd_repeat){
      send=false;
      $('#pwd_repeat').before('<div class="text-danger">Les mots de passe ne sont pas identiques </div>')
    }
    // --
    // -> Les mots de passe doivent être identique
    if (send){
      $.post(
        'ajax.php',
        $('form').serializeArray(),
        function(response){
          console.log(response);
          alert(response.message);
        }
      )
    }
}
