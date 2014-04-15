
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.

      google.setOnLoadCallback(drawChart);



      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart(id) {

      // Chart One

      if (id)
      {

      // Create the data table.
      var jsonData = jQuery.ajax({
        url: ajaxurl,
        dataType: "json",
        type: "POST",
        data: 'id='+id+'&action=formcraft_chart',
        async: false
      }).responseText;

    }
    else
    {

      // Create the data table.
      var jsonData = jQuery.ajax({
        url: ajaxurl,
        dataType: "json",
        data: 'action=formcraft_chart',
        async: false
      }).responseText;      

    }



      // Create our data table out of JSON data loaded from server.
      var data = new google.visualization.DataTable(jsonData);

      var d=new Date();

      var month=new Array();
      month[0]="January";
      month[1]="February";
      month[2]="March";
      month[3]="April";
      month[4]="May";
      month[5]="June";
      month[6]="July";
      month[7]="August";
      month[8]="September";
      month[9]="October";
      month[10]="November";
      month[11]="December";

      var options = {
        vAxis: {title: "Number"},
        hAxis: {title: "Day of"},
        seriesType: "bars",
        series: {1: {type: "line"}}
      };
      options.title='Recent Form Views and Submissions';
      options.hAxis.title='Date';


      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
      chart.draw(data, options);

    }



    function setupLabel() {

      if (jQuery('.label_check input').length) {
        jQuery('.label_check').each(function(){ 
          jQuery(this).removeClass('c_on');
        });
        jQuery('.label_check input:checked').each(function(){ 
          jQuery(this).parent('label').addClass('c_on');
        });                
      };
      if (jQuery('.label_radio input').length) {
        jQuery('.label_radio').each(function(){ 
          jQuery(this).removeClass('r_on');

        });
        jQuery('.label_radio input:checked').each(function(){ 
          jQuery(this).parent('label').addClass('r_on');
        });
      };
    };


    jQuery(document).ready(function () {

      jQuery("input.rand2").focus(function(){
        event.stopPropagation();
      });



      jQuery('body').on('click', '.delete-row', function() {

   if (confirm('Are you sure you want to delete the form? You can\'t undo this action.')) {


        if(jQuery(this).hasClass('btn-danger'))
        {
          var this_id = jQuery(this).attr('id');
          jQuery(this).button('loading');
          var id = jQuery(this).parent('td').parent('tr').attr('id');
          jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: 'action=formcraft_del&id='+id,
            success: function (response) {
              if (response=='Deleted')
              {
                jQuery('#'+this_id).button('complete');
                jQuery('#'+this_id).removeClass('btn-danger');
                jQuery('#'+this_id).addClass('btn-success');
              }
              else
              {
                jQuery('#'+this_id).button('reset');
              }
            },
            error: function (response) {
              alert("There was an error.");
            }
          });
        }



   }

      });


      jQuery('body').on('click', '.row_click', function() {
        var id = jQuery(this).parent('tr').attr('id');
        window.location.href = 'admin.php?page=survey_builder&id='+id;
      });

      // Edit Form Name and Description
      jQuery("body").on('click', '.edit_btn', function(event){
        event.stopPropagation();
        jQuery(this).hide();
        jQuery(this).parent().children('.rand').hide();

        var name = jQuery(this).prev('a').html();
        jQuery(this).prev('input.rand2').show();
        jQuery(this).prev('input.rand2').focus();
        jQuery(this).next('a.save_btn').show();
      });

      jQuery('body').on('click','.rand2',function(event){
        event.stopPropagation();
      });

      jQuery("body").on('click', '.save_btn', function(event){
        event.stopPropagation();
        jQuery(this).hide();
        var this_id = jQuery(this).attr('id');
        var id = jQuery(this).attr('id').split('_');
        var val = jQuery(this).parents().children('.rand2').val();

        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: 'action=formcraft_name_update&name='+val+'&id='+id[1],
          success: function (response) 
          {
            if (response=='D')
            {
              jQuery('#'+this_id).parent().children('.rand').text(val);
              jQuery('#'+this_id).parent().children('input.rand2').hide();
              jQuery('#'+this_id).parent().children('.rand').show();
              jQuery('#'+this_id).parent().children('.edit_btn').show();

            }
            else
            {
              jQuery('#'+this_id).show();
              jQuery('#'+this_id).parent().children('input.rand2').hide();
              jQuery('#'+this_id).parent().children('.rand').show();
              jQuery('#'+this_id).parent().children('.edit_btn').show();
            }
          },
          error: function (response) 
          {
           jQuery('#'+this_id).show();
         }
       });


      });



jQuery('#stats_select').change(function(){
  var val = jQuery(this).val();
  drawChart(val);
})

setupLabel();
jQuery('body').addClass('has-js');
jQuery('body').on("click",'.label_check, .label_radio' , function(){
  setupLabel();
});




});