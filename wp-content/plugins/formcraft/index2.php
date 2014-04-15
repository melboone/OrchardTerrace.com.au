<?php

global $wpdb;
$table_builder = $wpdb->prefix . "formcraft_builder";
$table_subs = $wpdb->prefix . "formcraft_submissions";



// load Angular JS
wp_deregister_script('angularjs');
wp_register_script( 'angularjs', 'http://ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js');
wp_enqueue_script('angularjs');

wp_register_script( 'angularjss', '//ajax.googleapis.com/ajax/libs/angularjs/1.0.2/angular-sanitize.min.js');
wp_enqueue_script('angularjss');

// Load jQuery
wp_deregister_script('jQuery');
wp_register_script('jQuery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', true, '1.8.3', false);
wp_enqueue_script('jQuery');

// Google Charts
wp_enqueue_script( 'charts', 'https://www.google.com/jsapi');

$exp = plugins_url('/formcraft/php/export2.php?id=');

// Load Main JS file
wp_enqueue_script( 'mainjs', plugins_url( 'js/js.js', __FILE__ ));
wp_localize_script( 'mainjs', 'Url', array( 'exporturl' => plugins_url('/formcraft/php/export.php') ) );
wp_localize_script( 'mainjs', 'MyAjax', array( 'ajaxurl' => plugins_url( '/formcraft/php/save.php' ) ) );
wp_enqueue_script( 'form_index_js', plugins_url( 'js/form_index.js', __FILE__ ));

// Load DatePicker Plugin JS
wp_enqueue_script( 'datejs', plugins_url( 'datepicker/js/datepicker.js', __FILE__ ));

// Load Custom jQuery UI Js
wp_enqueue_script( 'custom-js', plugins_url( 'formcraft/ui/js/jquery-ui-1.9.2.custom.min.js', dirname(__FILE__) ) );

// Load DataTables JS
wp_enqueue_script( 'tables', plugins_url( 'datatables/media/js/jquery.dataTables.min.js', __FILE__ ));

// Load Bootstrap JS
wp_enqueue_script( 'bootjs', plugins_url( 'bootstrap/js/bootstrap.min.js', __FILE__ ));


// Load Bootstrap CSS
wp_enqueue_style( 'bootcss', plugins_url( 'bootstrap/css/bootstrap.min.css', __FILE__ ));
wp_enqueue_style( 'facss', plugins_url( 'css/font-awesome/css/font-awesome.min.css', __FILE__ ));

// Load Main CSS
wp_enqueue_style( 'main', plugins_url( 'css/style.css', __FILE__ ));

// Load Raio and Check Styling CSS
wp_enqueue_style( 'boxes_style', plugins_url( 'css/boxes.css', __FILE__ ));

// Load DatePicker CSS
wp_enqueue_style( 'date_style', plugins_url( 'datepicker/css/datepicker.css', __FILE__ ));


$myrows = $wpdb->get_results( "SELECT * FROM $table_builder ORDER BY id" );
$mysub = $wpdb->get_results( "SELECT * FROM $table_subs ORDER BY id", 'ARRAY_A' );
$mysubr = $wpdb->get_results( "SELECT * FROM $table_subs WHERE seen='1'", 'ARRAY_A' );


?>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>

<script>
function PrintElem(elem)
{
  Popup($(elem).html());
}

function Popup(data) 
{
  var mywindow = window.open('', 'my div', 'height=400,width=600');
  mywindow.document.write('<html><title>FormCraft Submission</title>');
  mywindow.document.write('<body>');
  mywindow.document.write(data);
  mywindow.document.write('</body></html>');

  mywindow.print();
  mywindow.close();

  return true;
}
</script>


<script>

jQuery(document).ready(function () {

if ((document.domain=='ncrafts.net') || (document.domain=='www.ncrafts.net'))
{

	setTimeout(function() 
	{
		jQuery('#new_form_pop').trigger('click');
	},10);

}

});


	jQuery(function () {
		jQuery('#myTab a:last').tab('show');
	});

</script>

<div class="ffcover_add">


	<div id="title_div">	
		<h1>FormCraft</h1>
		<a class='docs_title' href='http://ncrafts.net/formcraft/docs/table-of-contents/' target='_blank' style='right: 16.5%'>Complete Online Guide</a>
		<a class='docs_title' href='<?php echo plugins_url('formcraft/documentation.html'); ?>' target='_blank' style='right: 6.5%'>Documentation</a>
		<a class='docs_title' href='http://ncrafts.net' target='_blank' style='right: 1.5%'>nCrafts</a>
	</div>




	<form class="modal hide fade" id='new_form' action='javascript:submit_new_form();'>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Add Form</h3>
		</div>
		<div class="modal-body">

			<label class='label_radio circle-ticked' for='new_r1' >
				<input type='radio' id='new_r1' value='new' checked name='type_form'><div class='label_div' style='background: #fff'>New Form</div>
			</label>
			<label class='label_radio circle-ticked' for='new_r2' >
				<input type='radio' id='new_r2' value='duplicate' name='type_form'><div class='label_div' style='background: #fff'>Duplicate</div>
			</label>



			<select name='duplicate' style='height: 30px'>
				<?php foreach ($myrows as $row) {
					?>
					<option value='<?php echo $row->id; ?>'><?php echo $row->name; ?></option>
					<?php } ?>

				</select>


				<hr>





				<label for='form_name_1'>Name of Form</label>
				<input id='form_name_1' name='name' type="text" class="input-small" autofocus placeholder='Site Feedback' style='width: 220px'>

				<br><br>
				<label for='form_desc_1'>Description (optional)</label>
				<textarea id='form_desc_1' name='desc' style='width: 220px' rows='4'></textarea>
				<br><br>


			</div>
			<div class="modal-footer">
				<span class='response_ajax'></span>
				<a href="#" class="btn" data-dismiss="modal">Close</a>
				<button type="submit" id='submit_new_btn' class="btn btn-success"><i class='icon-plus icon-white'></i> Add Form</button>
			</div>
		</form>






		<?php 
		$saw['today'] = 0;
		$saw['month'] = 0;

		foreach ($mysub as $key => $row) 
		{

			$dt = date_parse($row['added']);
			$date = date_parse(date('d M Y (H:m)'));

			if ($dt['month']==$date['month'] && $dt['day']==$date['day'] && $dt['year']==$date['year'])
			{
				$saw['today']++;
			}
			if ($dt['month']==$date['month'] && $dt['year']==$date['year'])
			{
				$saw['month']++;
			}
		} 

		?>



		<ul class="nav nav-pills" style='margin-top: 80px'>
			<li class='active'><a href="#home" data-toggle="tab">Home</a></li>
			<li><a href="#forms" data-toggle="tab">Forms</a></li>
			<li><a href="#submissions" data-toggle="tab">Submissions <span style='color: green'>(<?php echo sizeof($mysub)-sizeof($mysubr); ?>)</span></a></li>
			<li><a href="#files" data-toggle="tab">File Manager</a></li>
			<li><a href="#add" data-toggle="tab">Add-Ons</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="home">

				<div class='charts'>

					<select id='stats_select'>
						<option value='all'>All Forms</option>
						<?php
						foreach ($myrows as $row) {
							?>
							<option value='<?php echo $row->id; ?>'><?php echo $row->name; ?></option>
							<?php } ?>

						</select>

						<div id="chart_div" class='chart_js'></div>
					</div>
				</div>
				<div class="tab-pane" id="forms">		

					<div class='group_cover'>

						<a class='btn btn-success' id='new_form_pop' data-toggle='modal' href='#new_form' style='margin-left: 10px; margin-bottom: 10px; font-weight: bold; font-size: 15px; padding: 10px 20px'><i class='icon-plus icon-white'></i> Add Form</a>

						<div id='existing_forms'>
							<div class='subs_wrapper'>
								<table style='table-layout: fixed' class='table table-hover table-striped' id='ext'>
									<thead>
										<tr>
											<th width='1%' style='text-align: center; width: 5px'>ID</th>
											<th width='29%'>Name of Form</th>
											<th width='24%'>Description</th>
											<th width='12%' style='text-align: center'>Shortcode</th>
											<th width='7%' style='text-align: center'>Views</th>
											<th width='7%' style='text-align: center'>Submissions</th>
											<th width='13%' style='text-align: center'>Date Added</th>
											<th width='7%' style='text-align: center'>Options</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($myrows as $row) {
											?>
											<tr id='<?php echo $row->id; ?>'>
												<td class='row_click' style='text-align: center'><?php echo $row->id; ?></td>

												<td class='row_click'><a class='rand' href='admin.php?page=survey_builder&id=<?php echo $row->id; ?>'><?php echo $row->name; ?></a><input class="rand2" style="width: 110px; display:none; margin-right: 6px" type="text" value="<?php echo $row->name; ?>"><a class='btn edit_btn' title='Edit Form Name' id='edit_<?php echo $row->id; ?>'>edit</a><a class='btn save_btn' id='edit_<?php echo $row->id; ?>'>save</a></td>

												<td class='row_click row_description'><a  class='rand'><?php echo $row->description; ?></a></td>

												<td style='text-align: center; border-right: 1px solid #eee'>[formcraft id='<?php echo $row->id; ?>']</td>
												<td class='row_click' style='text-align: center'><?php echo $row->views; ?></td>
												<td class='row_click' style='text-align: center'><?php echo $row->submits; ?></td>
												<td class='row_click'><?php echo $row->added; ?></td>
												<td style='text-align: center; border-right: 1px solid #eee'>


													<a style='width: 30px; float: right; box-sizing: border-box; padding: 4px 0px' class='delete-row btn btn-danger' data-loading-text='...' data-complete-text="<i class='icon-ok icon-white'></i>" id='delete_<?php echo $row->id; ?>' title='Delete this form'><i class='icon-remove icon-white'></i>
													</a>
													<a href='<?php echo $exp.$row->id; ?>' class='btn btn-success' target='_blank' title='Export all submissions for this form' style='float: right; box-sizing: border-box; margin-right: 7px; padding: 4px 0px; width: 30px'><i class='icon-share-alt icon-white'></i> </a>
												</td>
											</tr>
											<?php } ?>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="submissions">			
						<div class='group_cover'>
							<div style='border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 10px'>
								<span class='stat'>
									<span class='unr_msg' id='unr_ind'><?php echo sizeof($mysub)-sizeof($mysubr); ?>
									</span> unread&nbsp;&nbsp;
									<span class='tot_msg' id='tot_ind'><?php echo sizeof($mysub); ?>
									</span> total	
								</span>
								<span class='stat'>
									<span class='unr_msg'><?php echo $saw['today']; ?>
									</span> new today&nbsp;&nbsp;
									<span class='tot_msg'><?php echo $saw['month']; ?>
									</span> new this month
								</span>
								<a class='btn btn-success' id='export' style='margin-left: 30px' title='Export all submissions data to CSV'>
									Export Data to CSV
								</a>
							</div>

							<div id='subs_c' >

								<table style='table-layout: fixed' class='table-sub table table-hover' id='subs'>
									<thead>
										<tr>
											<td width="10%" title='Click to sort'>ID</td>
											<td width="10%" title='Click to sort'>Read</td>
											<td width="20%" title='Click to sort'>Date</td>
											<td width="30%" title='Click to sort'>Form Name</td>
											<td width="20%" title='Click to sort'>Message</td>
											<td width="10%" title='Click to sort'>Options</td>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($mysub as $key=>$row) {
											$std= "style='padding: 4px 8px; margin: 0; vertical-align: top'";

											$new = json_decode($row['content'],1);

											$row_id = $row['form_id'];
											$mysub2 = $wpdb->get_results( "SELECT name FROM $table_builder WHERE id='$row_id'", 'ARRAY_A' );

											?>

											<tr id='sub_<?php echo $row['id']; ?>' class='<?php if ($row['seen']=='1') {echo 'row_shade';}?>'>
												<td style='text-align: center'><?php echo $row['id']; ?></td>
												<td style='text-align: center' id='rd_<?php echo $row['id']; ?>'><?php if($row['seen']) {echo 'Read';} else {echo 'Unread';} ?></td>
												<td style='text-align: center'><?php echo $row['added']; ?></td>
												<td><?php if (!(empty($mysub2[0]['name']))) {echo $mysub2[0]['name'];} else {echo '(form deleted)';}?></td>
												<td style='text-align: center'>
													<button class='btn view_mess' id='upd_<?php echo $row['id']; ?>' data-toggle='modal' data-target='#view_modal'>View</button>

												</td>
												<td style='text-align: center'>
													<i class='icon-trash icon-2x view_mess' id='del_<?php echo $row['id']; ?>' title='Delete message'></i>&nbsp;
													<i class='icon-bookmark-empty icon-2x view_mess' id='read_<?php echo $row['id']; ?>' title='Mark as unread'></i>
												</td>
											</tr>
											<?php } ?>

										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="files">				
							<?php

							$url = plugins_url('formcraft/file-upload/server/php/index.php');

							$ch = curl_init($url);

							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

							$read = curl_exec($ch);

							curl_close($ch);

							$read = json_decode($read, 1);
							?>



							<div class='group_cover'>

								<span class='stat' style='border: none'>
									<span class='unr_msg' id='unr_ind'><?php echo sizeof($read['files'])?>
									</span> files&nbsp;&nbsp;
								</span>

								<div id='files_c' >
									<div class='subs_wrapper'>

										<table style='table-layout: fixed' class='table-sub table table-hover' id='files_manager_table'>
											<thead>
												<tr>
													<td width="5%">#</td>
													<td width="20%">Name</td>
													<td width="10%">Size</td>
													<td width="59%">Url</td>
													<td width="6%">Delete</td>
												</tr>
											</thead>
											<tbody>
												<?php
												foreach ($read['files'] as $key => $value) 
												{
													?>

													<tr>
														<td><?php echo $key+1 ?></td>
														<td><?php echo $value['name']; ?></td>
														<td><?php echo round(($value['size']/1024),2); ?> KB</td>
														<td><a href='<?php echo $value['url']; ?>' target='_blank'><?php echo $value['url']; ?></a></td>
														<td><a class='btn btn-danger delete_from_manager' style='width: 38px' data-loading-text='...' data-url='<?php echo $value['url']; ?>' data-complete-text='<i class="icon-ok icon-white"></i>' id='del_fm_<?php echo $key+1 ?>'><i class='icon-remove icon-white'></i></a></td>
													</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>



							<div class="tab-pane" id="add">
							<?php

							if (defined('FORMCRAFT_ADD'))
							{
								formcraft_add_content();
							}
							else
							{




							?>
							<br>
<div style='width: 100%; margin: auto auto; text-align: center; font-size: 24px; color: #666; font-weight: 300; line-height: 132%'>
Install <a href='http://wordpress.org/plugins/formcraft-add-on-pack/' target='_blank'>FormCraft Add-On Pack</a><br>
<span style='font-size: 18px'>
(MailChimp, AWeber and Campaign Monitor integration)</span>
</div>

								<?php }

								?>



							</div>	

						</div>



					</div><!-- End of Cover -->
					<?php
					foreach ($mysub as $key=>$row) {
						$mess[$key] = '';
						$std  = "style='padding: 4px 8px; margin: 0; vertical-align: top; width: 30%; display: inline-block'";
						$std2 = "style='padding: 4px 8px; margin: 0; vertical-align: top; width: 60%; display: inline-block'";

						$new = json_decode($row['content'],1);
						$att = 1;

						foreach ($new as $value)
						{
							if ( !(empty($value['type'])) && !($value['type']=='captcha') && !($value['label']=='files') && !($value['type']=='hidden') && !($value['label']=='divider') )
							{
								if ( ($value['type']=='radio' || $value['type']=='check' || $value['type']=='stars' || $value['type']=='smiley') && (empty($value['value'])) )
								{
									$mess[$key] .= "";
								}
								else
								{
									$mess[$key] .= "<li><span $std><strong>$value[label] </strong></span><span $std2>$value[value]</span></li>";
								}
							}
							else if ($value['label']=='files') 
							{
								$mess[$key] .= "<li><span $std><strong>Attachment($att) </strong></span><a href='$value[value]' target='_blank' $std2>$value[value]</a></li>";
								$att ++;
							}
							else if ($value['label']=='divider') 
							{
								$mess[$key] .= "<hr>$value[value]<hr>";
								$att ++;
							}
							else if ($value['type']=='hidden' && $value['label']=='location') 
							{
								$location = "<div class='location_show'>$value[value]</div>";
								$att ++;
							}

						}


						$message[$key] = 
						'<ul>
						'.urldecode($mess[$key]).'
					</ul>';
					$row_id = $row['form_id'];
					$mysub2 = $wpdb->get_results( "SELECT name FROM $table_builder WHERE id='$row_id'", 'ARRAY_A' );


					?>

					<span style='display: none' id='upd_name_<?php echo $row['id']; ?>'><?php if (!(empty($mysub2[0]['name']))) {echo $mysub2[0]['name'];} else {echo '(form deleted)';}?></span>
					<span style='display: none' id='upd_text_<?php echo $row['id']; ?>'><p><?php echo $location.$message[$key]; ?></p></span>


					<?php
				}
				?>




				<div class='hid modal hide fade' id='view_modal' aria-hidden="true">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style='margin-right: 15px;margin-top: 8px'>×</button>
						<div id='print_area'>
					<div class="modal-header">
						<h3 class="myModalLabel"></h3>
					</div>
					<div class="modal-body" id='vm_body'>
						<p></p>
					</div>
					</div>
					<div class="modal-footer">
		<button value="Print Div" class='btn btn-primary' onclick="PrintElem('#print_area')" />Print
		</button>
						<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

					</div>
				</div>