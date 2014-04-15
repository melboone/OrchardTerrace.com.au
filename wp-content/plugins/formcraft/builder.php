<?php 
global $wpdb;
$table_builder = $wpdb->prefix . "formcraft_builder";
$table_subs = $wpdb->prefix . "formcraft_submissions";

if (defined('MYMAIL_VERSION'))
{
	?>
	<style>
		.mymail_email
		{
			display: block;
		}
	</style>
	<?php
}
else
{
	?>
	<style>
		.mymail_email
		{
			display: none !important;
		}
	</style>
	<?php
}



global $wpdb;
$id = $_GET['id'];
$qry = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id = '$id'" );
foreach ($qry as $row) {
	$build = stripcslashes($row->build);
	$options = stripcslashes($row->options);
	$con = stripcslashes($row->con);
	$rec = stripcslashes($row->recipients);
}
$conf = json_decode($con);

if (!isset($conf[0]->cap_width)) {$conf[0]->cap_width='active';}
if (!isset($conf[0]->subl)) {$conf[0]->subl='active';}
if (!isset($conf[0]->theme)) {$conf[0]->theme='none';}
if (!isset($conf[0]->themef)) {$conf[0]->themef='';}

if ($conf[0]->cap_width=='relative') 
	{$cap_width2 = 'active';} else 
{$cap_width = 'active';}

if ($conf[0]->subl=='subl') 
	{$subl2 = 'active';} else 
{$subl = 'active';}


$theme1 = '';
$theme2 = '';
$theme3 = '';
$theme4 = '';
$theme5 = '';
$theme6 = '';


if ($conf[0]->bg_image=='none') 
	{$theme1 = 'active';}

if ($conf[0]->bg_image==plugins_url('formcraft/images/wash.png')) 
	{$theme2 = 'active';}

if ($conf[0]->bg_image==plugins_url('formcraft/images/jean.png')) 
	{$theme3 = 'active';}

if ($conf[0]->bg_image==plugins_url('formcraft/images/debut.png')) 
	{$theme4 = 'active';}

if ($conf[0]->bg_image==plugins_url('formcraft/images/carbon.png')) 
	{$theme6 = 'active';}



if ($conf[0]->themev=='one') 
	{$themev1 = 'active';}

else if ($conf[0]->themev=='two') 
	{$themev2 = 'active';}

else if ($conf[0]->themev=='three') 
	{$themev3 = 'active';}

else
	{$themev3 = 'active';}




if ($conf[0]->themef=='') 
	{$themef1 = 'active';}

else if ($conf[0]->themef=='transparent') 
	{$themef2 = 'active';}

else if ($conf[0]->themef=='curvy') 
	{$themef3 = 'active';}

else
	{$themef1 = 'active';}




if ($conf[0]->block_label=='block_label') 
	{$block_label2 = 'active';}

else
	{$block_label1 = 'active';}



/////////////////// CSS //////////////////////

// BootStrap CSS
wp_enqueue_style( 'bootcss', plugins_url( 'bootstrap/css/bootstrap.min.css', __FILE__ ));
wp_enqueue_style( 'facss', plugins_url( 'css/font-awesome/css/font-awesome.min.css', __FILE__ ));

wp_enqueue_style( 'time_style', plugins_url( 'time/css/bootstrap-timepicker.min.css', __FILE__ ));

// jQuery UI CSS
wp_enqueue_style( 'jQuery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ));

// Form Builder CSS
wp_enqueue_style( 'formcraft_builder_css', plugins_url( 'css/style.css', __FILE__ ));

// Radio and CheckBox CSS
wp_enqueue_style( 'boxes_style', plugins_url( 'css/boxes.css', __FILE__ ));
wp_enqueue_style( 'ratings_style', plugins_url( 'css/ratings.css', __FILE__ ));

// Form CSS
wp_enqueue_style( 'formcraft_forms_css', plugins_url( 'css/nform_style.css', __FILE__ ));


// ColorPicker
wp_enqueue_style( 'colorpicker_css', plugins_url( 'colorpicker/spectrum.css', __FILE__ ));


/////////////////// JavaScripts //////////////////////




wp_enqueue_script('jquery');
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('my-upload');

wp_enqueue_style('thickbox');



// Load Angular JS
wp_deregister_script('angularjs');
wp_register_script( 'angularjs', plugins_url( 'js/angular.min.js', __FILE__ ));
wp_enqueue_script('angularjs');

wp_register_script( 'angularjss', plugins_url( 'js/angular-sanitize.min.js', __FILE__ ));
wp_enqueue_script('angularjss');

// JSON parser and encoder
wp_enqueue_script( 'json.jq', plugins_url( 'js/jquery.json.js', __FILE__ ));

// Inflate Deflate
wp_enqueue_script( 'deflate1', plugins_url( 'js/deflate/easydeflate.js', __FILE__ ));
wp_enqueue_script( 'deflate2', plugins_url( 'js/deflate/deflateinflate.min.js', __FILE__ ));
wp_enqueue_script( 'deflate3', plugins_url( 'js/deflate/typedarrays.js', __FILE__ ));
wp_enqueue_script( 'deflate4', plugins_url( 'js/deflate/json3.min.js', __FILE__ ));
wp_enqueue_script( 'deflate5', plugins_url( 'js/deflate/es5-shim.min.js', __FILE__ ));
wp_enqueue_script( 'deflate6', plugins_url( 'js/deflate/base64.js', __FILE__ ));






// Main Form Builder JS
$ul = plugins_url();
wp_enqueue_script( 'mainjs', plugins_url( 'js/js.js', __FILE__ ));
wp_localize_script( 'mainjs', 'J', array( 'B' => $build, 'O' => $options, 'C' => $con, 'R' => $rec, 'I' => $ul, 'ide' => $id,  'countries' => plugins_url('/formcraft/data/countries.json'),  'states' => plugins_url('/formcraft/data/states.json'),  'languages' => plugins_url('/formcraft/data/languages.json')  ) );

// BootStrap JS
wp_enqueue_script( 'bootjs', plugins_url( 'bootstrap/js/bootstrap.min.js', __FILE__ ), array('jquery-ui-core','jquery-ui-widget','jquery-ui-slider','jquery-ui-sortable','jquery-ui-widget') );

wp_enqueue_script( 'js_lib', plugins_url( 'libraries/js_libraries.js', __FILE__ ));

// Forms Main JS
wp_enqueue_script( 'formcraftjs', plugins_url( 'js/form.js', __FILE__ ));
wp_localize_script( 'formcraftjs', 'FormCraftJS', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'server' => plugins_url('/formcraft/file-upload/server/php/'), 'locale' => plugins_url('/formcraft/datepicker/js/locales/'), 'other' => plugins_url('/formcraft') ) );

wp_enqueue_script( 'formcraft-build-js', plugins_url( 'js/build.js', __FILE__ ));
wp_localize_script( 'formcraft-build-js', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// jQuery and Angular UI JS
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script( 'angular-ui-js', plugins_url( 'js/angular-ui.js', __FILE__ ));

// File Upload JS
wp_enqueue_script( 'upload-iframe-js', plugins_url( 'file-upload/js/jquery.iframe-transport.js', __FILE__ ));
wp_enqueue_script( 'upload-widget-js', plugins_url( 'file-upload/js/vendor/jquery.ui.widget.js', __FILE__ ));


// ColorPicker
wp_enqueue_script( 'jquery-colorpicker-s', plugins_url( 'colorpicker/spectrum.js', __FILE__ ));





?>
<script language="JavaScript">
	jQuery(document).ready(function() {

		var formfield;

		jQuery('.cpicker').spectrum({
			showInput: true,
			showAlpha: true,
			clickoutFiresChange: true,
			preferredFormat: 'rgb',
			showButtons: false,
			change: function(color){
				jQuery(this).trigger('input');
			},
			move: function(color){
				jQuery(this).trigger('input');
			}
		});

		jQuery('.custom_css_text').keyup(function()
		{
			var abc = jQuery(this).val();
			jQuery('.custom_css_show').text('<style>'+abc+'</style>');
		})



		jQuery('body').on('click', '.upload_logo_formpage', function() {
			formfield = jQuery(this).prev('input');
			tb_show('','media-upload.php?TB_iframe=true');
			return false;
		});


		window.old_tb_remove = window.tb_remove;
		window.tb_remove = function() {
			window.old_tb_remove();
			formfield=null;
		};


		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html){
			if (formfield) {
				fileurl = "url("+jQuery('img',html).attr('src')+")";
				jQuery(formfield).val(fileurl);
				jQuery(formfield).trigger('input');
				tb_remove();
			} else {
				window.original_send_to_editor(html);
			}
		};

	});
</script>
<!--[if IE]>
<style>
.main_builder textarea, .main_builder input[type="text"], .main_builder input[type="password"], .main_builder input[type="date"], .main_builder input[type="month"], .main_builder input[type="week"], .main_builder input[type="number"], .main_builder input[type="email"], .main_builder input[type="url"], .main_builder input[type="search"], .main_builder input[type="tel"], .main_builder .uneditable-input
{
	min-height: 28px !important;
}
</style>
<![endif]-->

<div ng-app="compile" ng-controller="bob_the_builder" class="ffcover bootstrap">


	<table class='ff_c_t'>
		<tr>
			<td style='width: 580px'>
				<div class="main_builder">
					<div class='build_affix' data-spy="affix" data-offset-top="0"><!-- Start of affixed Part -->

						<div class='head_holder'>
							<a class='sbtn' href='?page=survey_builder'><i class='icon-chevron-left icon-white'></i> List of Forms</a>


							<a data-loading-text="saving.." data-error-text='Retry' class='sbtn' ng-click='save()' id='save_form_btn' style='width: 100px'><i class='icon-folder-open icon-white'></i> Save </a>

							<a class="sbtn btn-toggle collapsed" data-toggle="collapse" href="#collapseOne"><i class='icon-wrench icon-white'></i> Form Options
							</a>

							<a class="sbtn btn-toggle collapsed" data-toggle="collapse"  href='#collapseTwo'><i class='icon-pencil icon-white'></i> Styling
							</a>

						</div>

						<div id="collapseOne" class="form_accordion accordion-body collapse">
							<span class='options_label'>Options</span>
							<div class="accordion-inner">



								<div class='accordion acl' id="accordion_fo">



									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_one">
												1. General
											</a>
										</div>

										<div id="form_options_one" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>

													<span class='settings_desc' style='font-size: 14px'>Submit values from fields hidden by Conditional Laws?</span>

													<label class='label_radio circle-ticked'>
														<input type='radio' ng-model='con[0].cl_hidden_fields' value='submit_hidden' name='cl_hidden_fields'>
														<div class='label_div' style='background: #f3f3f3'>
														Yes, submit all fields, whether hidden or visible</div>
													</label>

													<label class='label_radio circle-ticked'>
														<input type='radio' ng-model='con[0].cl_hidden_fields' value='no_submit_hidden' name='cl_hidden_fields'>
														<div class='label_div' style='background: #f3f3f3'>
														No</div>
													</label>
												</div>
												<div class='global_holder'>

													<span class='settings_desc' style='font-size: 14px'>Save form data as the user types? When the user comes back to the page, he can continue with the auto saved form.</span>

													<label class='label_radio circle-ticked'>
														<input type='radio' ng-model='con[0].user_save_form' value='save_form' name='user_save_form'><div class='label_div' style='background: #f3f3f3'>
														Yes</div>
													</label>

													<label class='label_radio circle-ticked'>
														<input type='radio' ng-model='con[0].user_save_form' value='no_save_form' name='user_save_form'><div class='label_div' style='background: #f3f3f3'>
														No</div>
													</label>

												</div>

											</div>
										</div>
									</div>

									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_two">
												2. Email Notifications
											</a>
										</div>

										<div id="form_options_two" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>

													<div class='gh_head'>Email Sending Method&nbsp;&nbsp;<button class='sbtn sbtn-small' id='test_email'><i class='icon-envelope'></i> Send Test Email</button></div>
													<div id='test_response'>
														<ol>
															<li>Save the form before sending a test email
															</li>
															<li>The test email(s) will be sent to the list of recipients added below</li>
														</ol>
													</div>

													<label class='label_radio circle-ticked'><input type='radio' ng-model='con[0].mail_type' value='mail' name='type_email'><strong><div class='label_div' style='background: #f3f3f3'>Use PHP Mail Function (default)</div></strong>
													</label>
													<br>
													<div class='mail_type_div {{con[0].mail_type}}1'>

														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Sender Name: </span><input type='text' ng-model='con[0].from_name'></label><span style='font-size: 11px; color: #888'> (optional field)</span><br>

														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Sender Email: </span><input type='email' ng-model='con[0].from_email'></label><span style='font-size: 11px; color: #888'> (optional field)</span>
														<br><br>
													</div>

													<label class='label_radio circle-ticked'><input type='radio' ng-model='con[0].mail_type' name='type_email' value='smtp'><strong><div class='label_div' style='background: #f3f3f3'>Use Authentication (try if the above method doesn't work)</div></strong></label>
													<br>

													<div class='mail_type_div {{con[0].mail_type}}'>

														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Sender Name: </span><input type='text' ng-model='con[0].smtp_name' placeholder='No Reply'></label><br>
														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Username: </span><input type='text' ng-model='con[0].smtp_email' placeholder='noreply@ncrafts.net'></label><br>
														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Password: </span><input type='password' ng-model='con[0].smtp_pass'></label><br>
														<label><span style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'> Host: </span><input type='text' ng-model='con[0].smtp_host' placeholder='mail.ncrafts.net'></label>

                                                        <label class='label_radio circle-ticked' style='margin-left: 35%'><input type='checkbox' ng-model='con[0].if_ssl' name='if_ssl' ng-true-value='ssl' ng-false-value='false'><div class='label_div' style='background: #f3f3f3'>Use SSL</div></label>

													</div>		
												</div>

												<div class='global_holder'>

													<label for='error_id_a' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Email Subject</label>
													<input type='text' id='error_id_a' style='width: 60%' ng-model='con[0].email_sub'>

												</div>


												<div class='global_holder'>
													<div class='gh_head'>Add Email Recipients</div>
													<span class='settings_desc'>When the form is successfully submitted, the following people will get an email notification</span>
												</p>
												<ul class='rec_ul'>
													<li>
														<input type='text' ng-model='rec' style='width: 310px;'>
														<button class='sbtn sbtn-small' ng-click='addRec()'><i class='icon-plus icon-white'></i> Add</button>
													</li>

													<li ng-repeat='name in recipients'>
														<button class='btn btn-danger btn-mini del-btn2' ng-click='remRec($index)' style='width: 29px'><i class='icon-remove icon-white'></i>
														</button>									
														<span compile='name.val' class='recipients'></span>
													</li>

												</ul>
											</div>

										</div>
									</div>
								</div>





								<div class="accordion-group">
									<div class="accordion-heading">
										<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_three">
											3. Email AutoResponders
										</a>
									</div>

									<div id="form_options_three" class="accordion-body collapse">
										<div class="accordion-inner l2">

											<div class='global_holder'>
												<div class='gh_head'>
													AutoReply Email Settings
												</div>
												<span class='settings_desc'>You can send autoreplies to emails entered by the user when they fill up the form. You can enable autoreplies for any email form field, by checking the option <strong>Send AutoReply to this Email</strong> in the field options.</span>
												<br>


												<p>Subject of Email<br>
												</p>
												<input ng-model='con[0].autoreply_s' style='width: 100%' type='text'>
												<br><br>

												<p>Body of Email<br>
													<span class='settings_desc'>you can use HTML here</span>
												</p>
												<textarea ng-model='con[0].autoreply' rows='7' style='width: 100%'>
												</textarea>
												<div compile='con[0].autoreply' style='white-space: pre-line'>
												</div>

											</div>
											
										</div>
									</div>
								</div>




									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_four">
												4. How to Use the Form
											</a>
										</div>

										<div id="form_options_four" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>

													<div class='gh_head'>Dedicated Form Page &nbsp;&nbsp;
														<label class='label_check'>
															<input type='checkbox' ng-model='con[0].formpage'><div class='label_div' style='background: #f3f3f3'>Enabled</div>
														</label>
													</div>

													<div class='op_{{con[0].formpage}}'>
														Show a Logo On the Form Page
														<input id="image_location" type="text" name="image_location" placeholder='Paste URL here or click Select' ng-model="con[0].formpage_image" style='width: 74%'/>
														<input  class="btn btn-primary upload_logo_formpage" type="button" value="Select Image" style='width: 25%'/>
														<br><br>
														The below URL links to page that contains the form. You can share this URL to allow people to fill this form / survey. This page also looks awesome on mobile devices.
														<div class='well well-small code'>
															<?php echo plugins_url().'/formcraft/form.php?id='.$_GET['id']; ?>
														</div>
													</div>

												</div>
												<div class='global_holder'>
													<div class='gh_head'>
														Other Methods
													</div>

													Shortcode
													<div class='well well-small code'>[formcraft id='<?php echo $_GET['id']; ?>']</div>
													Widget
													<div class='well well-small code'>Go to <em class='surround'>Appearance -> Widgets -> formcraft</em></div>
													Use in Themes, and other places

													<div class='well well-small code'>&lt;?php formcraft(<?php echo $_GET['id']; ?>); ?&gt;</div>
													Please refer to the <a href='<?php echo plugins_url('formcraft/documentation.html#usage'); ?>' target='_blank' title='opens in a new tab / window'>documentation</a> for details on how to use the sticky, popup, or fly in forms
												</div>
											</div>
										</div>
									</div>

									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_five">
												5. Form Error Messages
											</a>
										</div>

										<div id="form_options_five" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>


													<label for='error_id_a' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Common Message</label>
													<input type='text' id='error_id_a' style='width: 60%' ng-model='con[0].error_gen'>
													<hr>

													<label for='error_id_1' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Incorrect File Type (for file uploads)</label>
													<input type='text' id='error_id_1' style='width: 60%' ng-model='con[0].error_ftype'>
													<br>
													<label for='error_id_1' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Incorrect Email</label>
													<input type='text' id='error_id_1' style='width: 60%' ng-model='con[0].error_email'>
													<br>
													<label for='error_id_2' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Incorrect URL</label>
													<input type='text' id='error_id_2' style='width: 60%' ng-model='con[0].error_url'>
													<br>
													<label for='error_id_3' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Incorrect Captcha</label>
													<input type='text' id='error_id_3' style='width: 60%' ng-model='con[0].error_captcha'>
													<br>
													<label for='error_id_4' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Integers Only</label>
													<input type='text' id='error_id_4' style='width: 60%' ng-model='con[0].error_only_integers'>
													<br>
													<label for='error_id_5' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Compulsory Field</label>
													<input type='text' id='error_id_5' style='width: 60%' ng-model='con[0].error_required'>
													<br>
													<label for='error_id_6' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Min Characters</label>
													<input type='text' id='error_id_6' style='width: 60%' ng-model='con[0].error_min'>
													<br>
													<label for='error_id_7' style='width: 30%; display: inline-block; text-align: right; margin-right: 5%'>Max Characters</label>
													<input type='text' id='error_id_7' style='width: 60%' ng-model='con[0].error_max'>
													<br>

												</div>
											</div>
										</div>
									</div>



									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_six">
												6. On Form Submission
											</a>
										</div>

										<div id="form_options_six" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>
													<div class='gh_head'>
														Form Sent Message
													</div>

													<span class='settings_desc'>you can use HTML here</span>
												</p>
												<textarea style='width:96%; margin: 0' rows='4' ng-model='con[0].success_msg'></textarea>
												<div compile='con[0].success_msg' class='nform_res_sample' style='white-space: pre-line'></div>
											</div>
											<div class='global_holder'>

												<div class='gh_head'>
													Form Could Not be Sent Message
												</div>														<span class='settings_desc'>you can use HTML here</span>
											</p>
											<textarea style='width:96%;  margin: 0' rows='4' ng-model='con[0].failed_msg'></textarea>
											<div compile='con[0].failed_msg' class='nform_res_sample' style='white-space: pre-line'></div>

										</div>
										<div class='global_holder'>
											<div class='gh_head'>
												Redirection
											</div>

											<p>URL<br>
												<span class='settings_desc'>redirects the user in case of a successful form submission<br>(disabled in the form builder mode)</span>
											</p>
											<input type='text' style='width: 96%' ng-model='con[0].redirect'>

										</div>
									</div>
								</div>
							</div>



							<div class="accordion-group">
								<div class="accordion-heading">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_seven">
										7. Support FormCrafts
									</a>
								</div>

								<div id="form_options_seven" class="accordion-body collapse">
									<div class="accordion-inner l2">

										<div class='global_holder'>

											<span style='color: #444; font-size: 15px;'>
												Display a referral link below the submit button
											</span>

											<br><br>
											<label style='display: inline-block; width: 120px'>Text</label>
											<input type='text' ng-model='con[0].rlink' placeholder='Powered by FormCrafts'>
											<br>

											<label style='display: inline-block; width: 120px'>Username</label>
											<input type='text' ng-model='con[0].ruser' placeholder='nCrafts'>
											<em style='font-size: 12px; color: #888; line-height: 14px;'> 
												<br>
												(Enter your marketplace username here. You will receive a 30% commission on the first deposit / purchase made by user who clicked on this link. Read more <a href='http://codecanyon.net/make_money/affiliate_program' target="_blank">here</a>.)</em>


											</div>
										</div>
									</div>
								</div>


								<?php
if (function_exists('formcraft_add_builder'))
{
	formcraft_add_builder();
}

								?>





								<?php
								if (defined('MYMAIL_VERSION'))
								{
									?>


									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_mymail" style='color: green'>
												9. Integration: MyMail
											</a>
										</div>

										<div id="form_options_mymail" class="accordion-body collapse">
											<div class="accordion-inner l2">

												<div class='global_holder'>
												<div class='gh_head'>
												MyMail
												</div>
												<span class='settings_desc'>You can use the forms to easily add to your list of subscribers with MyMail.
													<br><br>
													<p>Step 1 of 2
													<span class='settings_desc'>
													Enter the name of the list you want to add the new email subscribers to</span>
													</p>
													<input ng-model='con[0].mm_list' style='width: 100%' type='text'><br><br>
													<p>Step 2 of 2
													<span class='settings_desc'>
													Now add an email field and check the option 'Add to MyMail'.<br>That's it!
													</span>
													</p>
												</div>
											</div>
										</div>
									</div>


									<?php } ?>
									</div>


							</div>
						</div>

						<div id="collapseTwo" class="form_accordion accordion-body collapse">
							<span class='options_label'>Styling</span>
							<div class="accordion-inner">

								<div class="accordion acl" id="accordion2">
									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#globalOne">
												1. Overall Form Styling
											</a>
										</div>

										<div id="globalOne" class="accordion-body collapse">
											<div class="accordion-inner l2">
												<div class='global_holder'>

													<label for='form_wd' class='option_text'>Form Width</label>
													<input id='form_wd' type='text' ng-model='con[0].fw' style='width: 60px'>
													<span class='description' style='width: 200px'>Enter a value like 400px, 500px or 70%.<br> Or leave it blank.</span>
													<br>
													<label class='option_text'>Rounded Corners</label>
													<div id='slider_rc' class='con_slider'></div>
													<span style='font-size: 15px'>{{con[0].fr}}px</span>
													<input id='slider_rc_v' type='number' min='0' ng-model='con[0].fr' style='display: none'>
													<br>
													<label class='option_text'>Form Frame</label>
													<label class='label_check'>
														<input type='checkbox' ng-model='con[0].frame' ng-true-value='noframe'><div class='label_div' style='background: #f3f3f3'> Remove Frame</div>

													</label>
													<br>

													<label class='option_text'>Form Layout</label>
													<label class='label_radio'>
														<input type='radio' ng-model='con[0].flayout' value='horizontal'>
														<div class='label_div' style='background: #f3f3f3'>Horizontal</div>
													</label>
													<label class='label_radio'>
														<input type='radio' ng-model='con[0].flayout' value='vertical'>
														<div class='label_div' style='background: #f3f3f3'>Vertical</div>
													</label>
													<br>




													<label class='option_text' style='width: 150px; display: inline-block'> Field Alignment </label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].field_align' value='left' name='field_align'>
														<div class='label_div' style='background: #f3f3f3'>Left</div>
													</label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].field_align' value='center' name='field_align'>
														<div class='label_div' style='background: #f3f3f3'>Center</div>
													</label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].field_align' value='right' name='field_align'>
														<div class='label_div' style='background: #f3f3f3'>Right</div>
													</label>


													<label class='option_text' style='width: 150px; display: inline-block'> Direction </label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].direction' value='ltr' name='direction'>
														<div class='label_div' style='background: #f3f3f3'>Left to Right</div>
													</label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].direction' value='rtl' name='direction'>
														<div class='label_div' style='background: #f3f3f3'>Right to Left</div>
													</label>


												</div>

												<div class='global_holder'>

													<label class='option_text'>Transparent Background</label>
													<label class='label_check'>
														<input type='checkbox' ng-model='con[0].bg_transparent' ng-true-value='inherit'> <div class='label_div' style='background: #f3f3f3'>Transparent</div>
													</label>
													<br>

													<span class='option_text'>Form Background</span>
													<span class='btn-group ' data-toggle='buttons-radio'>
														<button type='button' class='btn btn-primary <? echo $theme1; ?> tool' ng-click='con[0].bg_image="none"'>None</button>

														<button type='button' class='btn btn-primary <? echo $theme2; ?> tool' ng-click='con[0].bg_image="url(<?php echo plugins_url('formcraft/images/wash.png') ?>)"'>Linen</button>

														<button type='button' class='btn btn-primary <? echo $theme3; ?> tool' ng-click='con[0].bg_image="url(<?php echo plugins_url('formcraft/images/jean.png') ?>)"'>Jean</button>

														<button type='button' class='btn btn-primary <? echo $theme4; ?> tool' ng-click='con[0].bg_image="url(<?php echo plugins_url('formcraft/images/debut.png') ?>)"'>Debut</button>

														<button type='button' class='btn btn-primary <? echo $theme6; ?> tool' ng-click='con[0].bg_image="url(<?php echo plugins_url('formcraft/images/carbon.png') ?>)"'>Carbon</button>

													</span>


													<span class='option_text'>Custom Background</span>
													<span class=''>
														<input id="image_location_2" type="text" name="image_location_2" placeholder='Paste URL here or click Select' ng-model="con[0].bg_image" style='width: 44%'/>
														<input  class="btn btn-primary upload_logo_formpage" type="button" value="Select"/>
													</span>


													<label class='option_text'>Font Family</label>
													<select ng-model='con[0].formfamily'>
														<option></option>
														<option>Arial</option>
														<option>Arial Black</option>
														<option>Courier New</option>
														<option>Times New Roman</option>
														<option>Trebuchet MS</option>
														<option>Verdana</option>
													</select>											
												</div>



											</div>

										</div>
									</div>
									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#globalTwo">
												2. Input Fields
											</a>
										</div>
										<div id="globalTwo" class="accordion-body collapse">										
											<div class="accordion-inner l2">
												<div class='global_holder'>
													<label class='option_text' for='slider_fs_v'> Field Spacing </label>
													<div id='slider_fs' class='con_slider'></div>
													<span style='font-size: 15px'>{{con[0].space}}px</span>
													<input class='mr' type='number' min='0' id='slider_fs_v' ng-model='con[0].space' style='width: 48px; font-size: 13px; display: none'>


													<label class='option_text' for='slider_fs_v'> Font Size </label>
													<div id='slider_font' class='con_slider'></div>
													<span style='font-size: 15px'>{{con[0].field_font}}px</span>
													<input class='mr' type='number' min='0' id='slider_font_v' ng-model='con[0].field_font' style='width: 48px; font-size: 13px; display: none'>


													<label class='option_text'>Font Color</label>
													<input type='text' ng-model='con[0].input_color' style='width: 168px' class='cpicker'>
												</div>

												<div class='global_holder'>
													<label class='option_text' for='form_title_px'> Full Length Labels</label>
													<span class='btn-group' data-toggle='buttons-radio'>
														<button type='button' class='btn btn-primary <? echo $block_label1; ?> tool' ng-click='con[0].block_label="no_block_label"'>No</button>
														<button type='button' class='btn btn-primary <? echo $block_label2; ?> tool' ng-click='con[0].block_label="block_label"'>Yes</button>
													</span>
													<span class='description' style='width: 40%'>Yes: input fields will appear below the labels, instead of sitting next to them.</span>
													<br>





												</div>


												<div class='global_holder'>
													<span class='option_text'>Validation Style</span>
													<span class='btn-group' data-toggle='buttons-radio'>
														<button type='button' class='btn btn-primary <? echo $themev1; ?> tool' ng-click='con[0].themev="one"'>Right</button>
														<button type='button' class='btn btn-primary <? echo $themev3; ?> tool' ng-click='con[0].themev="three"'>Top</button>
													</span>
													<br>
													<label class='label_check'>

														<input type='checkbox' ng-model='con[0].show_star_validation'>
														<div class='label_div' style='background: #f3f3f3'>Don't Show 
														<span class='show_1_sample'>*</span>
														for Required Fields</div>

													</label>
												</div>


												<div class='global_holder'>
													<span class='option_text'>Input Fields Theme</span>
													<span class='btn-group ' data-toggle='buttons-radio'>
														<button type='button' class='btn btn-primary <? echo $themef1; ?> tool' ng-click='con[0].themef=""'>None</button>
														<button type='button' class='btn btn-primary <? echo $themef2; ?> tool' ng-click='con[0].themef="transparent"'>Transparent</button>
														<button type='button' class='btn btn-primary <? echo $themef3; ?> tool' ng-click='con[0].themef="curvy"'>Curvy</button>
													</span>
												</div>

											</div>
										</div>
									</div>




									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#globalThree">
												3. Labels and Sub-Labels
											</a>
										</div>
										<div id="globalThree" class="accordion-body collapse">										
											<div class="accordion-inner l2">

												<div class='global_holder'>
													<div class='gh_head'>General</div> 


													<label class='option_text' style='width: 150px; display: inline-block'> Label Placement </label>


													<label class='label_radio'><input type='radio' ng-model='con[0].placeholder' name='radio_placeholder' value='placeholder'>
													<div class='label_div' style='background: #f3f3f3'>Inside / Hidden</div>
													</label>

													<label class='label_radio'><input type='radio' ng-model='con[0].placeholder' name='radio_placeholder' value='no_placeholder'>
													<div class='label_div' style='background: #f3f3f3'>Outside
													</div>
													</label>



												</div>

												<div class='global_holder'>
													<div class='gh_head'>Main Labels</div> 
													<span class='option_text'>
														Width of input labels</span>
														<span class='btn-group ' data-toggle='buttons-radio'>
															<button type='button' class='btn btn-primary <? echo $cap_width; ?> tool' ng-click='con[0].cap_width=""'>Fixed</button>
															<button type='button' class='btn btn-primary <? echo $cap_width2; ?> tool' ng-click='con[0].cap_width="relative"'>Relative</button>
														</span>
														<div class='brk'></div>


														<label for='lfs' class='option_text'>Font Size</label>
														<input type='number' min='1' id='lfs' ng-model='con[0].lfs' style='width: 60px'>
														<label for='lfc' class='option_text'>Font Color</label>
														<input class='cpicker' id='lfc' ng-model='con[0].lfc' style='width: 40px'>
													</div>


													<div class='global_holder'>
														<div class='gh_head'>Sub Labels</div> 
														<span class='option_text'>Show sub-labels?</span>
														<span class='btn-group ' data-toggle='buttons-radio'>
															<button type='button' class='btn btn-primary <? echo $subl; ?> tool' ng-click='con[0].subl=""'>Yes</button>
															<button type='button' class='btn <? echo $subl2; ?> btn-primary tool' ng-click='con[0].subl="subl"'>No</button>
														</span>
														<div class='brk'></div>

														<label for='slfs' class='option_text'>Font Size</label>
														<input type='number' min='1' id='slfs' ng-model='con[0].slfs' style='width: 60px'>
														<label for='slfc' class='option_text'>&nbsp; &nbsp;Font Color</label>
														<input class='cpicker' id='slfc' ng-model='con[0].slfc' style='width: 40px'>
													</div>

												</div>
											</div>
										</div>


										<div class="accordion-group">
											<div class="accordion-heading">
												<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#globalFour">
													4. Custom CSS
												</a>
											</div>


											<div id="globalFour" class="accordion-body collapse">	
												<div class="accordion-inner l2">

													<div class='global_holder'>
														<div class='gh_head'>Enter Custom CSS</div> 
														<textarea style='width: 100%' ng-model='con[0].custom_css' rows='8' class='custom_css_text'></textarea>

													</div>
												</div>

											</div>									

										</div>

									</div>


								</div>




							</div>



							<div class='choose_from_main'>
								<span class="choose_from main btn-group">
									<button type="button" class="btn btn-star" ng-click="addEl('stars')">Star</button>
									<button type="button" class="btn btn-smiley" ng-click="addEl('smiley')">Smiley</button>
									<button type="button" class="btn btn-star" ng-click="addEl('thumbs')">Thumb</button>

									<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
										Choice Matrix
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu">
										<li><a ng-click="addEl('matrix2')">Two Column</a></li>
										<li><a ng-click="addEl('matrix3')">Three Column</a></li>
										<li><a ng-click="addEl('matrix4')">Four Column</a></li>
										<li><a ng-click="addEl('matrix5')">Five Column</a></li>
									</ul>
								</span>
								<span class='choose_from btn-group'>

									<button type="button" class="btn" ng-click="addEl('upload')" style='width: 52px'>File</button>
									<button type="button" class="btn" ng-click="addEl('check')" title='CheckBoxes'>CheckBox</button>
									<button type="button" class="btn" ng-click="addEl('radio')" title='Radios'>MultiChoice</button>
									<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
										Slider
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu">
										<li><a ng-click="addEl('slider')">Slider</a></li>
										<li><a ng-click="addEl('slider-range')">Range</a></li>
									</ul>
								</span>

								<span class='choose_from btn-group'>
									<button type="button" class="btn" ng-click="addEl('email')">Email</button>
									<button type="button" class="btn" ng-click="addEl('text')">One Line Text</button>
									<button type="button" class="btn" ng-click="addEl('para')">Multi Line</button>
									<button type="button" class="btn" ng-click="addEl('dropdown')">Dropdown</button>
									<button type="button" class="btn" ng-click="addEl('date')">Date Input</button>
									<button type="button" class="btn" ng-click="addEl('image')">Image</button>
								</span>

								<span class='choose_from btn-group'>
									<button type="button" class="btn" ng-click="addEl('divider')">Divider</button>
									<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
										Other
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu">
										<li><a ng-click="addEl('time')">TimePicker</a></li>
										<li><a ng-click="addEl('hidden')">Hidden Field</a></li>
										<li><a ng-click="addEl('captcha')">Captcha</a></li>
										<li><a ng-click="addEl('custom')">Custom</a></li>
									</ul>
								</span>




							</div>
							<ul id='well' class='accordion'>


								<div style='position: relative; margin: 0' id='edit_title' class='nform_edit_div'>
									<li class='accordion-group'>
										<div class="accordion-heading">
											<div class="edit_head" href="#collapse_title" id='acc_title' data-parent='#well_accordion'>Form Title</div>
										</div>
										<div id="collapse_title" class="accordion-body">
											<div class="accordion-inner">
												<div class='opt_cl' style='padding-top: 12px; padding-left: 20px'>
													<label class='option_text' for='form_title_field' style='width: 150px; display: inline-block'>Form Title </label>
													<input class='mr' type='text' id='form_title_field' ng-model='con[0].form_title'>

													<br>

													<label class='option_text' for='form_title_px' style='width: 150px; display: inline-block'> Font Size </label>
													<input class='mr' type='number' min='1' id='form_title_px' ng-model='con[0].ft_px' style='width: 50px'>
													<br>

													<label for='title134' style='width: 150px; display: inline-block'>Font Style</label>
													<select id='title134' ng-model='con[0].tfamily' style='width: 150px; height: auto; padding: 5px'>
														<option></option>
														<option>Arial</option>
														<option>Arial Black</option>
														<option>Courier New</option>
														<option>Times New Roman</option>
														<option>Trebuchet MS</option>
														<option>Verdana</option>
													</select>&nbsp;&nbsp;&nbsp;


													<label class='label_radio' for='tbold_1'><input id='tbold_1' type='radio' ng-model='con[0].tbold' value='bold' name='tbold'>
														<div class='label_div' style='background: #fff'>Bold</div>
													</label>
													<label class='label_radio' for='tbold_2'><input id='tbold_2' type='radio' ng-model='con[0].tbold' value='normal' name='tbold'>
														<div class='label_div' style='background: #fff'>Normal</div>
													</label>

													<br>

													<label class='option_text' style='width: 150px; display: inline-block'> Font Alignment </label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].ftalign' value='left' name='ftalign'>
														<div class='label_div' style='background: #fff'>Left</div>
													</label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].ftalign' value='center' name='ftalign'>
														<div class='label_div' style='background: #fff'>Center</div>
													</label>

													<label class='label_radio'>
														<input type='radio' ng-model='con[0].ftalign' value='right' name='ftalign'>
														<div class='label_div' style='background: #fff'>Right</div>
													</label>
													<br>

													<label class='option_text' style='width: 150px; display: inline-block'> Font Color </label>
													<input class='cpicker mr' ng-model='con[0].ft_co' style='width: 30px'>

													<br>

													<label class='option_text' style='width: 150px; display: inline-block'> Background Color </label>
													<input class='cpicker mr' ng-model='con[0].ft_bg' style='width: 30px'>
													<span class='description' style='width: 100px'>Set the form theme to <strong>none</strong> before using this.</span>


												</div>

											</div>
										</div>
									</li>
								</div>

								<div ng-repeat="el in build" style='position: relative; margin: 0' id='edit_{{$index}}' class='nform_edit_div'>
									<li id='{{$index}}' class='accordion-group'>
										<div class="accordion-heading" style='height: 38px'>

											<div class="edit_head" href="#collapse{{$index}}" compile="el.el_b" id='acc{{$index}}' data-parent='#well_accordion'></div>

											<button class='btn btn-danger min-btn' id='{{$index}}' title='Minimize'><i class='icon-minus icon-white'></i></button>

											<button class='btn btn-danger del-btn' id='db_{{$index}}' ng-click='remEl($index)' title='Delete Field'  ng-disabled="el.isDisabled"><i class='icon-trash icon-white'></i></button>
										</div>
										<div id="collapse{{$index}}" class="accordion-body">
											<div class="accordion-inner" compile="el.el_b2"></div>
										</div>
									</li>

								</div>
							</ul>
						</div><!-- End of affixed Part -->
					</div><!-- End of Left Part -->
				</td>
				<td style='vertical-align: top'>
					<div class="preview_form">


						<h4>You can <strong>drag</strong> and <strong>drop</strong> the fields to change their order.<br>Click to edit them.
							<a href='<?php echo plugins_url().'/formcraft/form.php?id='.$_GET['id'].'&preview=true'; ?>' target='_blank'>Live Preview (new window)</a>
						</h4>
						<div class='html_here'><?php $form_id = "123$_GET[id]"; ?><form class="a_<?php echo $form_id; ?> nform bootstrap {{con[0].cl_hidden_fields}} {{con[0].user_save_form}} {{con[0].placeholder}} {{con[0].frame}} {{con[0].direction}} {{con[0].theme}} {{con[0].themev}} {{con[0].themef}} {{con[0].block_label}} star_{{con[0].show_star_validation}} {{con[0].flayout}}" action='javascript:submit_formcraft(<?php echo $form_id; ?>);' ng-style='{width: con[0].fw, borderRadius: con[0].fr+"px", MozBorderRadius: con[0].fr+"px", WebkitBorderRadius: con[0].fr+"px", fontFamily: con[0].formfamily, backgroundImage: con[0].bg_image, background: con[0].bg_transparent}' id='<?php echo $form_id; ?>'><input type='hidden' class='form_id' val='<?php echo $_GET['id']; ?>' ng-model='con[0].form_main_id'><input type='hidden' class='getlocation' val='' name='location_hidden__0_0_1000_'><div id='fe_title' class='nform_li form_title {{con[0].theme}}' ng-style='{fontSize: con[0].ft_px+"px", borderTopLeftRadius: con[0].fr+"px", MozBorderTopLeftRadius: con[0].fr+"px", WebkitBorderTopLeftRadius: con[0].fr+"px", borderTopRightRadius: con[0].fr+"px", MozBorderTopRightRadius: con[0].fr+"px", WebkitBorderTopRightRadius: con[0].fr+"px", color: con[0].ft_co, backgroundColor: con[0].ft_bg, fontFamily: con[0].tfamily, fontWeight: con[0].tbold, backgroundImage: con[0].bg_image, background: con[0].bg_transparent, textAlign: con[0].ftalign}'>{{con[0].form_title}}</div><ul class='form_ul clearfix {{con[0].theme}}' id='form_ul'><li ng-repeat="el in build" ng-style='{paddingBottom: con[0].space+"px", paddingTop: con[0].space+"px"}' id='fe_{{$index}}_<?php echo $form_id; ?>' class='nform_li {{el.default}} {{el.inline}} {{con[0].field_align}} {{el.li_class}} fe_{{$index}}'><div compile="el.el_f" ng-style='{marginBottom: el.divspa, marginTop: el.divspa}'></div><span class='element_id'>{{$index}}</span></ul><div id='fe_submit' class='form_submit' style='position: relative; display: block'><span style='text-align: center; display: inline-block; padding-top: 8px; padding-bottom: 4px'><a class='ref_link' ng-href='http://codecanyon.net/item/formcraft-premium-wordpress-form-builder/5335056?ref={{con[0].ruser}}' compile="con[0].rlink" target='_blank' title='Opens in a new tab'></a></span><div class='res_div'><span class='nform_res'></span></div></div></form>
					</div>
				</div><!-- End of Right Part -->
			</td>
		</tr>
	</table>
</div><!-- End of Cover -->
