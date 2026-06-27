function validation_inscription() {

    var nom = document.getElementById("nom");
    var pre = document.getElementById("pre");
    var age = document.getElementById("age");
    var num = document.getElementById("num");
    var email = document.getElementById("email");
    var address = document.getElementById("add");
    var password = document.getElementById("mdp");
    var confirmation = document.getElementById("confirmation");
    var wilaya = document.getElementById("w");
    var genderRadios = document.getElementsByClassName("s");

  var valnom=nom.value;
  var valpre=pre.value;
  var valage=age.value;
  var valnum=num.value;
  var valemail=email.value;
  var valaddress=address.value;
  var valpassword =password.value;
  var valconfirmation=confirmation.value; 
  var valwilaya=wilaya.value;
    var gender = "";
    for (var i = 0; i < genderRadios.length; i++) {
        if (genderRadios[i].checked) {
            gender = genderRadios[i].value;
            break;
        }
    }
  
    

    if (valnom == "") {
       var name = document.getElementById("name");
         name.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Entez votre nom !</td>";
        return false;
    }else{
     document.getElementById("name").innerHTML = "";
   }
     

    if (valpre == "") {
        var lastname = document.getElementById("lastname");
         lastname.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Entez votre prenom !</td>";
        return false;
    }else{
     document.getElementById("lastname").innerHTML = "";
   }
      

    if (valage == "" || valage < 17 || valage > 100) {
         var age1 = document.getElementById("age1");
         age1.innerHTML="<td colspan='2' style='color:red;text-align:center;'>l'âge doit être compris entre 17 et 100 ans !</td>";
        return false;
    }else{
     document.getElementById("age1").innerHTML = "";
   }
    if (valnum == "" || valnum.length < 9  || valnum.length > 10) {
         var num1= document.getElementById("num1");
         num1.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Numéro de téléphone invalide !</td>";
        
        return false;
    }else{
     document.getElementById("num1").innerHTML = "";
   }
    if (valemail == "") {
         var emaill= document.getElementById("emailll");
         emaill.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Entez votre email !</td>";
        
        
        return false;
   }else{
     document.getElementById("emailll").innerHTML = "";
   }
    if (valaddress == "") {
         var address= document.getElementById("address");
         address.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Entez votre adresse !</td>";
        
        return false;
    }else{
     document.getElementById("address").innerHTML = "";
   }
    if (valpassword == "") {
        var password= document.getElementById("password");
         password.innerHTML="<td colspan='2' style='color:red;text-align:center;'>Entez votre mot de passe !</td>";
        
        return false;
    }else{
     document.getElementById("password").innerHTML = "";
   }
    if (valpassword.length < 8) {
          var password= document.getElementById("password");
         password.innerHTML="<td colspan='2' style='color:red;text-align:center;'>le mot de passe doit comporter au moins 8 caractères !</td>";
       
        return false;
    }else{
     document.getElementById("password").innerHTML = "";
   }
    if (valconfirmation == "") {
         var confpassword= document.getElementById("confpassword");
         confpassword.innerHTML="<td colspan='2' style='color:red;text-align:center;'>confirmez votre mot de passe !</td>";
       
        return false;
    }else{
        document.getElementById("confpassword").innerHTML = "";
   }
    if (valconfirmation !== valpassword) {
         var confpassword= document.getElementById("confpassword");
         confpassword.innerHTML="<td colspan='2' style='color:red;text-align:center;'>les mots de passe ne correspondent pas !</td>";
       
        return false;
    }else{
     document.getElementById("confpassword").innerHTML = "";
   }
    if (gender == "") {
         var gender= document.getElementById("gender");
         gender.innerHTML="<td colspan='2' style='color:red;text-align:center;'>veuillez sélectionner votre sexe !</td>";
        
        return false;
   }else{
     document.getElementById("gender").innerHTML = "";
   }

    if (!confirm("Wilaya choisie : " + valwilaya + "\nconfirmez-vous votre choix ?")) {
        return false;
    }

    if (!confirm("Sexe choisi : " + gender + "\nconfirmez-vous votre choix ?")) {
        return false;
    }
 
    return true;
}