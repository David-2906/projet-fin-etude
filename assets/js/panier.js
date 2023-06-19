$(document).ready(function (){
    // ajout dans le panier

    // récupération de l'élément destiné à recevoir la modale de connexion
    let signInElement = document.getElementById("signInModal");
    let signInModal = null;

    // Création de la modale (instruction sur la page bootstrap)
    if (signInElement) {
        signInModal = new bootstrap.Modal(signInElement,{
            focus: true,
        });
    }

    let $addToCartBtn = $("add-to-cart-btn");

    let $needsConnexion = parseInt($addToCartBtn.attr('data-needsconnection'));

    let $formCardAdd = $(".formCartAdd")

    if($formCardAdd){
        $formCardAdd.on('submit', function (evt) {
            evt.preventDefault();
            // récuperons l'id du produit à ajouter au panier
            let $produitId = $('#addToCartForm .hidden-id').val();
            
            if ($needsConnexion) {
            // on est pas logué, on ouvre la modale:
            signInModal.show();
        } else {
            // on lance l'ajax d'ajout
            $.ajax({
                method: "POST",
                url: jsData.urls.addToCart,
                data: {
                    produitId: $produitId,
                },
                dataType: "json",
            })
            .done(function (resp) {
                // succes de la requete asynchrone
            if (resp.success) {
                let count = parseInt($('#navbarSupportedContent .badge').text());
                $('#navbarSupportedContent .badge').html(count +1);
            }
            })
            .fail(function (jqXHR, textStatus) {
                // echec de la requête asynchrone
            });
        }
        })
    }

    $('.cart-delete-link').on('click', function (e) {
        $(this).parent().submit();
    })
})