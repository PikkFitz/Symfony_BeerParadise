document.addEventListener('DOMContentLoaded', function () {
    // Sélectionnez les champs 'user' et 'adresse'
    const userField = document.querySelector('#Commande_user');
    const adresseField = document.querySelector('#Commande_adresse');

    const test2 = document.querySelector('#Commande_adresse-ts-dropdown');
    
    

    // Fonction pour charger les adresses en fonction de l'utilisateur sélectionné
    function loadAdresses() {
        console.log("change...")
        const userId = userField.value; // Récupérez l'ID de l'utilisateur sélectionné

        // Effectuez une requête AJAX pour récupérer les adresses de l'utilisateur
        // Remplacez 'url-to-your-endpoint' par l'URL de votre point de terminaison qui renvoie les adresses
        fetch('/user_adresses/' + userId)
            .then(response => response.json())
            .then(data => {
                // Supprimez les anciennes options
                adresseField.innerHTML = '';
                test2.innerHTML = "";

                // Ajoutez les nouvelles options
                data.forEach(adresse => {
                    const option = document.createElement('option');
                    option.value = adresse.id;
                    option.textContent = adresse.nom;
                    adresseField.appendChild(option);
                });
            });
    }

    // Écoutez l'événement de changement de l'utilisateur
    userField.addEventListener('change', loadAdresses);

    // Chargez les adresses initiales au chargement de la page
    loadAdresses();
});
