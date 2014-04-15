jQuery(document).ready(function () {

  // Hide all elements which should be hidden by default
  jQuery('.is_hidden').each(function(){
    var id = jQuery(this).attr('id');
    jQuery('#'+id).css('display','none');
  });


  jQuery('.is_hidden').hide();

 var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;



if (width<=740)
{

    jQuery('.add-on').each(function(){
    var he = jQuery(this).prev('input').outerHeight();
    he = parseInt(he);
    jQuery(this).css({'height':he+'px'});
    });

    jQuery('.c_image').each(function(){
    var he = jQuery(this).next('input').outerHeight();
    he = parseInt(he);
    jQuery(this).css({'height':he+'px'});
    });
}

  load_user_form();

});


  setInterval(function(){

    jQuery('.save_form').each(function()
    {
    var id = jQuery(this).attr('id');
    save_user_form(id);
    });

  }, 5000);