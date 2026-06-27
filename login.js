function Validation_login() {
    var email = document.getElementById("email");
    var password = document.getElementById("password");

    var valemail=email.value;
    var valpassword=password.value;

    if (valemail =="" && valpassword == "") {
        var element =  document.getElementById("element");
        element.innerHTML="<td colspan='2' style='color:red ;text-align:center;'>Entrez votre email et votre mot de passe !</td>";
         
        return false;
    } else if (valemail == "") {
        var element =  document.getElementById("element");
        element.innerHTML="<td colspan='2' style='color:red ;text-align:center;'>Entrez votre email !</td>";
         
        return false;
    } else if (valpassword == "") {
      var element =  document.getElementById("element");
        element.innerHTML="<td colspan='2' style='color:red ;text-align:center;'>Entrez votre mot de passe !</td>";
         
        return false;
    }
      element.innerHTML="";

   return true;
}
