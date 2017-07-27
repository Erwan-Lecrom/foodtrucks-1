"use strict";
$(document).ready(function(){
  //Afficher / Masquez le mot de passe
  $('.input-group-addon').click(function(){
    toggleViewPwd($(this).data('toggleView'));
  });
  // Envoie du formulaire

});
function toggleViewPwd(fieldid){
  var field= $('#'+fieldid);
  if(field.attr('type')=="password"){
      field.attr('type','text');
  }else if (field.attr('type')=="text"){
    field.attr('type','password');
  }
}
