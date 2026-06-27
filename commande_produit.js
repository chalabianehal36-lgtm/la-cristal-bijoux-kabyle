function validation_commande() {
   
    var q = document.getElementById("qu").value;

 if (!confirm("Quantité à acheter : " + q + "\nConfirmez-vous votre choix ?")) {
        return false;
    } else {
        return true;
    }
}