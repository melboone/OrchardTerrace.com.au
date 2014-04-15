<?php 

require('../../../wp-blog-header.php');

global $wpdb, $table_subs, $table_builder, $table_info;
    
$myrows = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id=$_GET[id]", "ARRAY_A" );
$con = stripslashes($myrows[0]['con']);
$con = json_decode($con, 1);

$table_info = $wpdb->prefix . "formcraft_info";


if ( (!is_user_logged_in() || $_GET['preview']!=true) )
{
	if (!$con[0]['formpage']=='1')
	{
		exit;
	}
}

?>

<html>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->


<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php echo $myrows[0]['name']; ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php 

wp_head();

?>

</head>



<body>
<?php 


$image = $con[0]['formpage_image'];

if ($image)
{
echo "<img class='logo_form' src='".$image."' style='margin: auto auto; display: block'/><br><br>";
}
else
{
	echo '<br>';
}

formcraft($_GET['id']);

do_action('wp_head');

?>
<br><br>
<style>
@media screen and (min-width: 960px) {
	body {
		background-color: #fff;
	}
}
.nform
{
	margin-right: auto;
	margin-left: auto;
}
</style>
</body>
</html>