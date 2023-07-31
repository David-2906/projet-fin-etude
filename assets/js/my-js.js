// $(function () {

//   //Check it the user has been accpeted the agreement
//   if (!document.cookie || document.cookie.search('modal=accepted') == -1)
//     $("#popup").show();

//   $('[data-popup-close]').on('click', function (e) {
//     var targeted_popup_class = jQuery(this).attr('data-popup-close');
//     $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

//     //Set a cookie to remember the state
//     document.cookie = "modal=accepted";

//     e.preventDefault();
//   });

// });



$(function () {
  // Cette partie du code est exécutée lorsque le DOM est prêt

  // Vérifier si l'utilisateur a accepté l'accord en vérifiant les cookies
  if (!document.cookie || document.cookie.search('modal=accepted') == -1) {
    // Si le cookie "modal" n'existe pas ou s'il ne contient pas la valeur "accepted",
    // alors le popup doit être affiché. Sinon, l'utilisateur a déjà accepté.

    // Afficher le popup en faisant apparaître l'élément avec l'ID "popup"
    $("#popup").show();
  }

  // Définir un gestionnaire d'événements pour les éléments ayant l'attribut "data-popup-close"
  $('[data-popup-close]').on('click', function (e) {
    // Lorsque l'utilisateur clique sur un élément avec "data-popup-close"

    // Récupérer la valeur de l'attribut "data-popup-close" de l'élément sur lequel l'utilisateur a cliqué
    var targeted_popup_class = jQuery(this).attr('data-popup-close');

    // Sélectionner l'élément ayant l'attribut "data-popup" égal à la valeur récupérée ci-dessus,
    // puis le faire disparaître progressivement (transition de 350 millisecondes)
    $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

    // Définir un cookie pour se souvenir que l'utilisateur a accepté l'accord en fermant le popup
    document.cookie = "modal=accepted";

    // Empêcher le comportement par défaut de l'élément sur lequel l'utilisateur a cliqué
    // (par exemple, empêcher un lien de rediriger vers une autre page)
    e.preventDefault();
  });
});