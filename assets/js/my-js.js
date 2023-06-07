$(function () {

  //Check it the user has been accpeted the agreement
  if (!document.cookie || document.cookie.search('modal=accepted') == -1)
    $("#popup").show();

  $('[data-popup-close]').on('click', function (e) {
    var targeted_popup_class = jQuery(this).attr('data-popup-close');
    $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

    //Set a cookie to remember the state
    document.cookie = "modal=accepted";

    e.preventDefault();
  });

});

