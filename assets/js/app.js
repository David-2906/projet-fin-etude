$(document).ready(function () {
    /**** Creation de la modale dans notre javascript ****/

    // recupération de l'element destiné à
    // recevoir la modale de connexion
    let signInElement = document.getElementById("signInModal");
    let signInModal = null;

    // Creation de la modale (instruction sur la page de doc bootstrap)
    if (signInElement) {
        signInModal = new bootstrap.Modal(signInElement, {
            focus: true,
        });
    }

    /**** Connexion à partir d'un click sur le lien 'se connecter' du header  ****/

    // on recupère dans des variables les liens de connexion et deconnexion
    // du header
    let $headerConnexionTrigger = $(".connexion-modal-trigger");

    // click sur le lien 'se connecter' du header
    if ($headerConnexionTrigger) {
        $headerConnexionTrigger.on("click", function (e) {
            signInModal.show();
        });
    }

    // on recupere le formulaire de login dans la modal et on
    // le bloque, puis on prépare l'ajax puis on le lance.
    $modalLoginForm = $(".modal-login-form");

    $modalLoginForm.on("submit", function (e) {
        e.preventDefault();

        // valeurs des formulaires
        email = $(".modal-login-form #inputEmail").val();
        password = $(".modal-login-form #inputPassword").val();
        csrf = $(".modal-login-form #csrf").val();
        spinner = $("#signInModal .spinner-border");
        errorNotificationZone = $("#signInModal .ajax-error-notif");

        // apparition du spinner
        spinner.removeClass("d-none");
        errorNotificationZone.addClass("d-none");

        // lancement de l'ajax
        $.ajax({
            method: "POST",
            url: jsData.urls.login,
            data: {
                email: email,
                password: password,
                _csrf_token: csrf,
            },
            dataType: "json",
        })
        .done(function (resp) {
            // succès de la requête asynchrone:
            //  console.log( "ajax success" );
            // console.log(resp);

            if (resp.success) {
                spinner.addClass("d-none");
                errorNotificationZone.addClass("d-none");
                window.location.reload();
            }

            if (!resp.success) {
                errorNotificationZone.html(resp.message);
                spinner.addClass("d-none");
                errorNotificationZone.removeClass("d-none");
            }
        })
        .fail(function (jqXHR, textStatus) {
            // échec de la requête asynchrone:
            // console.log( "Request failed: " + textStatus );
            // console.log(jqXHR);
            $("#connexionForm .notif-error").html(
                "Une erreur inattendue s'est produite"
            );
        });
    });

    
});