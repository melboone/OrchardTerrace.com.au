<?php  
    /* 
    Plugin Name: FormCraft
    Description: Premium WordPress form and survey builder. Make amazing forms, incredibly fast.
    Author: nCrafts
    Author URI: http://nCrafts.net/
    Plugin URI: http://codecanyon.net/item/formcraft-premium-wordpress-form-builder/5335056
    Version: 1.3
    */
    error_reporting(0);


    if (!isset($_SESSION)) {
        session_start();
    }

    global $wpdb, $table_builder, $table_subs, $table_stats, $table_info;
    $table_builder = $wpdb->prefix . "formcraft_builder";
    $table_subs = $wpdb->prefix . "formcraft_submissions";
    $table_stats = $wpdb->prefix . "formcraft_stats";
    $table_info = $wpdb->prefix . "formcraft_info";

    $restricted = array('10000','61','63','65','66','74','75','77','78','80','83','84','86','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25');

    add_action('wp_ajax_formcraft_update', 'formcraft_update');
    add_action('wp_ajax_nopriv_formcraft_update', 'formcraft_update');
    add_action('wp_ajax_formcraft_add', 'formcraft_add');
    add_action('wp_ajax_nopriv_formcraft_add', 'formcraft_add');
    add_action('wp_ajax_formcraft_del', 'formcraft_del');
    add_action('wp_ajax_nopriv_formcraft_del', 'formcraft_del');
    add_action('wp_ajax_formcraft_submit', 'formcraft_submit');
    add_action('wp_ajax_nopriv_formcraft_submit', 'formcraft_submit');
    add_action('wp_ajax_formcraft_sub_upd', 'formcraft_sub_upd');
    add_action('wp_ajax_nopriv_formcraft_sub_upd', 'formcraft_sub_upd');
    add_action('wp_ajax_formcraft_name_update', 'formcraft_name_update');
    add_action('wp_ajax_nopriv_formcraft_name_update', 'formcraft_name_update');
    add_action('wp_ajax_formcraft_delete_file', 'formcraft_delete_file');
    add_action('wp_ajax_nopriv_formcraft_delete_file', 'formcraft_delete_file');
    add_action('wp_ajax_formcraft_chart', 'formcraft_chart');
    add_action('wp_ajax_nopriv_formcraft_chart', 'formcraft_chart');
    add_action('wp_ajax_formcraft_increment', 'formcraft_increment');
    add_action('wp_ajax_nopriv_formcraft_increment', 'formcraft_increment');
    add_action('wp_ajax_formcraft_increment2', 'formcraft_increment2');
    add_action('wp_ajax_nopriv_formcraft_increment2', 'formcraft_increment2');

    add_action('wp_ajax_formcraft_test_email', 'formcraft_test_email');
    add_action('wp_ajax_nopriv_formcraft_test_email', 'formcraft_test_email');


    function formcraft_test_email()
    {

        global $wpdb, $table_builder;
        error_reporting(-1);
        $id = $_POST['id'];

        $qry = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id = '$id'", 'ARRAY_A' );
        foreach ($qry as $row) {
            $con = stripslashes($row['con']);
            $rec = stripslashes($row['recipients']);
        }
        $con = json_decode($con, 1);
        $rec = json_decode($rec, 1);
        if (sizeof($rec)==0)
        {
            echo "No email recipient added";
            die();
        }
        $sender_name = $con[0]['from_name'];
        $sender_email = $con[0]['from_email'];

        if ($con[0]['mail_type']=='smtp')
        {

            require_once("php/class.phpmailer.php");
            error_reporting(-1);

            foreach($rec as $send_to)
            {

                $to = $send_to['val'];

                $mail = new PHPMailer();

                $mail->IsSMTP();
                $mail->Host = $con[0]['smtp_host'];

                $mail->CharSet = 'UTF-8';

                $mail->SMTPAuth = true;
                $mail->Username = $con[0]['smtp_email'];
                $mail->Password = $con[0]['smtp_pass'];
                $mail->FromName = $con[0]['smtp_name'];
                $mail->AddAddress($to);
                $mail->From = $con[0]['smtp_email'];
                $mail->IsHTML(true);

                if ($con[0]['if_ssl']=='ssl')
                {
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;
                }

                $mail->Subject = 'Test Email from FormCraft';
                $mail->Body = 'Test Email from FormCraft';

                if ($mail->Send())
                {
                    echo 'Email Sent to the Recipients';
                    die();
                }

            }

        } // End of SMTP Email
        else
        {


            $headers = "From: $sender_name <$sender_email>\r\nReply-To: $sender_email\r\n";
            $headers.= 'MIME-Version: 1.0' . "\r\n";
            $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";

            $subject = 'Test Email from FormCraft';
            $message = 'Test Email from FormCraft';


            foreach($rec as $send_to)
            {
             $to = $send_to['val'];
             if (mail($to, $subject, $message, $headers))
             {
                echo 'Email Sent to the Recipients';
                die();
            }
        }
        } // End of PHP Function Email

        die();
    }



    function formcraft_increment2()
    {
        error_reporting(0);
        formcraft_increment($_POST['id']);
    }


    function formcraft_increment($id)
    {
        error_reporting(0);
        global $wpdb, $table_stats, $table_builder, $table_info;

        if (!isset($id))
        {
            if (isset($_POST['id']))
            {
                $id = $_POST['id'];
            }
            else if (isset($_GET['id']))
            {
                $id = $_GET['id'];
            }
        }


        $wpdb->query( "UPDATE $table_builder SET
            views = views + 1
            WHERE id = '$id'" );


        $insert = $wpdb->insert( $table_stats, array( 
            'id' => $id
            ) );

        $date2 = date('Y-m-d');

        $temp1 = $wpdb->query( "SELECT * FROM $table_info WHERE time = '$date2' AND id = $id " );

        if ($temp1>=1)
        {
            $wpdb->query( "UPDATE $table_info SET views = views + 1 WHERE id = $id AND time = '$date2' " );
        }
        else
        {
            $temp2 = $wpdb->insert( $table_info, array( 'time' => $date2, 'views' => 1, 'submissions' => 0, 'id' => $id ) );
        }



    }



    function formcraft_chart()
    {
        error_reporting(0);

        global $wpdb, $table_subs, $table_builder, $table_stats, $table_info;


        if (ctype_digit($_POST['id']))
        {
           $subs = $wpdb->get_results( "SELECT * FROM $table_info WHERE id = $_POST[id] ORDER BY time", "ARRAY_A" );
       }
       else
       {
           $subs = $wpdb->get_results( "SELECT * FROM $table_info ORDER BY time", "ARRAY_A" );        
       }


       foreach ($subs as $key => $value) 
       {
        if ($subs[$key]['time']==$subs[$key+1]['time'])
        {
            $subs[$key+1]['views'] = $subs[$key+1]['views']+$subs[$key]['views'];
            $subs[$key+1]['submissions'] = $subs[$key+1]['submissions']+$subs[$key]['submissions'];
            unset($subs[$key]);
        }
    }


    foreach ($subs as $key => $value)
    {

        $dt = date_parse($subs[$key]['time']);
        $diff_m = abs(($dt['month']-date('m'))*30);
        $diff_d = date('d')-$dt['day'];
        $month = date('Y-m-d');
        $diff = $diff_m+$diff_d;
        $subs[$key]['time'] = date("d M", strtotime($subs[$key]['time']));


        if ($diff<30)
        {
            $temp2 = array();
            $temp2[] = array('v' => (string) $subs[$key]['time'], 'f' => null); 
            $temp2[] = array('v' => (int) $subs[$key]['views'], 'f' => null); 
            $temp2[] = array('v' => (int) $subs[$key]['submissions'], 'f' => null); 
            $rows2[] = array('c' => $temp2);        
        }

    }


    echo '
    {
      "cols": 
      [
      {"id":"","label":"Day","pattern":"","type":"string"},
      {"id":"","label":"Views","pattern":"","type":"number"},
      {"id":"","label":"Submissions","pattern":"","type":"number"}
      ],
      "rows": 
      '.stripslashes(json_encode($rows2)).'}';
      die();

  }


  function formcraft_delete_file()
  {
    error_reporting(0);
    $url = $_POST['url'];
    $file_name = "../wp-content/plugins/formcraft/file-upload/server/php/files/".basename(urldecode($url));
    $file_name2 = "../wp-content/plugins/formcraft/file-upload/server/php/files/thumbnail/".basename(urldecode($url));

    if (is_file($file_name))
    {
        unlink($file_name);
        unlink($file_name2);
        echo "Deleted";
    }
    else
    {
        echo "Not";
    }
    die();
}

function formcraft_sub_upd()
{
    error_reporting(0);

    global $wpdb, $table_subs, $table_builder;

    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type=='upd')
    {
        $wpdb->query( "UPDATE $table_subs SET
          seen = '1'
          WHERE id = '$id'" );
    }
    else if ($type=='del')
    {
        if ($wpdb->query( "DELETE FROM $table_subs WHERE id = '$id'" ))
        {    
            echo 'D';
        }
    }
    else if ($type=='read')
    {
        if (
            $wpdb->query( "UPDATE $table_subs SET
              seen = NULL
              WHERE id = '$id'" )
            )
        {    
            echo 'D';
        }
    }


    die();

}

function formcraft_name_update()
{
    error_reporting(0);

    global $wpdb, $table_subs, $table_builder, $restricted;

    $id = $_POST['id'];
    $name = $_POST['name'];

    if (array_search($id, $restricted) && $_SERVER['HTTP_HOST'] == 'ncrafts.net')
    {
        die();
    }

    $wpdb->query( "UPDATE $table_builder SET
      name = '$name'
      WHERE id = '$id'" );

    echo 'D';



    die();

}






function formcraft_submit()
{
    error_reporting(0);
    global $errors;
    $conten = file_get_contents('php://input');
    $conten = explode('&', $conten);
    $nos = sizeof($conten);
    $title = $_POST['title'];
    global $id;
    $id = $_POST['id'];

    $i = 0;
    while ($i<$nos)
    {
        $cont = explode('=', $conten["$i"]);
        $content[$cont[0]]=$cont[1];
        $content_ex = explode('_',$cont[0]);
        if ( !($content_ex[0]=='id') && !($content_ex[0]=='action') )
        {
            $new[$i]['label'] = $content_ex[0];
            $new[$i]['value'] = urldecode($cont[1]);
            $new[$i]['type'] = $content_ex[1];
            $new[$i]['validation'] = $content_ex[2];
            $new[$i]['required'] = $content_ex[3];
            $new[$i]['min'] = $content_ex[4];
            $new[$i]['max'] = $content_ex[5];
            $new[$i]['tooltip'] = $content_ex[6];
            $new[$i]['custom'] = $content_ex[7];
            $new[$i]['custom2'] = $content_ex[8];
            $new[$i]['custom3'] = $content_ex[9];
            $new[$i]['custom4'] = $content_ex[10];
        }
        $i++;
    }

    // Get Form Options
    global $wpdb, $table_subs, $table_builder, $table_info;

    $qry = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id = '$id'", 'ARRAY_A' );
    foreach ($qry as $row) {
        $con = stripslashes($row['con']);
        $title = stripslashes($row['name']);
        $rec = stripslashes($row['recipients']);
    }



    $con = json_decode($con, 1);
    $rec = json_decode($rec, 1);


    // Run the Validation Functions
    $i = 0;

    $ar_inc = 1;
    while ($i<$nos)
    {
        if ($new[$i]['custom']=='autoreply')
            {$autoreply[$ar_inc]=$new[$i]['value']; $ar_inc++;}
        $new[$i]['custom3'] = 'zz'.$new[$i]['custom3'];





     // Prepare List for MailChimp
        if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'm')==true) )
         { $mc_add[]=$new[$i]['value'];}

     // Prepare List for AWeber
     if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'a')==true) )
        { $aw_add[]=$new[$i]['value'];}

     // Prepare List for Campaign Monitor
    if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'c')==true) )
        { $campaign_add[]=$new[$i]['value'];}

    // Prepare List for MyMail
    if ($new[$i]['type']=='email' && $new[$i]['custom4']=='true')
    {
        $mm_add[]=$new[$i]['value'];
        $mm++;
    }

    // Prepare List of Custom Variables for MC or MM
    if ( $new[$i]['type']!='email' && isset($new[$i]['custom']) )
    {
        if (!empty($new[$i]['value']))
        {
            $custom_var[$new[$i]['custom']] = $new[$i]['value'];            
        }
    }



    if ($new[$i]['custom2']=='replyto')
        {$replyto = $new[$i]['value'];}

    if ($new[$i]['type']=='upload' && $new[$i]['value']=='0')
        {$new[$i]['value']=null;}




    formcraft_no_val($new[$i]['value'], $new[$i]['required'], $new[$i]['min'], $new[$i]['max'], $new[$i]['tooltip'], $con[0]);


    if (function_exists('formcraft_'.$new[$i]['validation']))
    {
        $fncall = 'formcraft_'.$new[$i]['validation'];
        $fncall($new[$i]['value'], $new[$i]['validation'], $new[$i]['required'], $new[$i]['min'], $new[$i]['max'], $new[$i]['tooltip'], $con[0]);
    }

    $i++;
}


if( sizeof($errors) )
{
    if ($con[0]['error_gen']!=null)
    {
        $errors['errors'] = $con[0]['error_gen'];
    }
    else
    {
        $errors['errors'] = 'none';
    }
    $errors = json_encode($errors);
    echo $errors;
}
else
{   

    global $wpdb, $table_subs, $table_builder;

    $qry = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id = '$id'", 'ARRAY_A' );
    foreach ($qry as $row) {
        $con = stripslashes($row['con']);
    }
    $con = json_decode($con, 1);



    $sender_name = $con[0]['from_name'];
    $sender_email = $con[0]['from_email'];

    $success_sent = 0;

    // Add to MailChimp

    if (defined('FORMCRAFT_ADD'))
    {

        if ($con[0]['mc_double']=='true') {$con[0]['mc_double']=true;} else {$con[0]['mc_double']=false;}
        if ($con[0]['mc_welcome']=='true') {$con[0]['mc_welcome']=true;} else {$con[0]['mc_welcome']=false;}

        if ($con[0]['mc_list'] && isset($mc_add) && function_exists('mailchimp_fc'))
        {
            mailchimp_fc($mc_add, $custom_var, $con[0]['mc_list'], $con[0]['mc_double'], $con[0]['mc_welcome']);        
        }

        if ($con[0]['aw_list'] && isset($aw_add) && function_exists('aweber_fc'))
        {
            aweber_fc($aw_add, $custom_var, $con[0]['aw_list']);        
        }

        if ($con[0]['campaign_list'] && isset($campaign_add) && function_exists('campaign_fc'))
        {
            campaign_fc($campaign_add, $custom_var, $con[0]['campaign_list']);        
        }

    }


    // Add to MyMail
    if ( isset($con[0]['mm_list']) && defined('MYMAIL_VERSION'))
    {
        $template = 'notification.html';
        foreach ($mm_add as $mm_email)
        {
            mymail_subscribe($mm_email,$custom_var,$con[0]['mm_list'],NULL,true,NULL,$template);
        }
    }



    // Make the Email
    $label_style = "padding: 4px 8px 4px 0px; margin: 0; width: 180px; font-size: 13px; font-weight: bold";
    $value_style = "padding: 4px 8px 4px 0px; margin: 0; font-size: 13px";
    $divider_style = "padding: 10px 8px 4px 0px; margin: 0; font-size: 16px; font-weight: bold; border-bottom: 1px solid #ddd";

    $i=0;
    $att=1;

    $email_body = '';

    while ($i<$nos)
    {
        if ($new[$i]['label']!='files')
        {
            $new[$i]['label'] = urldecode($new[$i]['label']);
            $new[$i]['value'] = urldecode($new[$i]['value']);                    
        }

        if ( !(empty($new[$i]['type'])) && !($new[$i]['type']=='captcha') && !($new[$i]['type']=='hidden') && !($new[$i]['label']=='files') && !($new[$i]['label']=='divider') && !($new[$i]['type']=='radio') && !($new[$i]['type']=='check')  && !($new[$i]['type']=='smiley') && !($new[$i]['type']=='stars') && !($new[$i]['type']=='matrix') )
        {
            $email_body .= "<tr><td style='$label_style'> ".$new[$i]['label']."</td><td style='$value_style'>".$new[$i]['value']."</td></tr>";
        }
        else if ( $new[$i]['label']=='files' )
        {
            $email_body .= "<tr><td style='$label_style'>Attachment($att)</td><td style='$value_style'><a href='".$new[$i]['value']."'>".$new[$i]['value']."</a></td></tr></div>";
            $att++;
            echo $new[$i]['value'];
        }
        else if ( $new[$i]['label']=='divider' )
        {
            $email_body .= "</table><table style='border: 0px; color: #333; width: 100%'><tr><td style='$divider_style'>".$new[$i]['value']."</td></tr></table><table>";
        }
        else if ( $new[$i]['type']=='hidden' && $new[$i]['label']=='location' )
        {
            $email_body .= "<tr><td style='color: #999; padding-bottom: 10px'> ".$new[$i]['value']."</td></td></tr>";
        }
        else if (  $new[$i]['type']=='radio' || $new[$i]['type']=='check' || $new[$i]['type']=='smiley' || $new[$i]['type']=='stars' || $new[$i]['type']=='matrix' )
        {
            if ( $new[$i]['value']==true )
            {
                $email_body .= "<tr><td style='$label_style'>".$new[$i]['label']."</td><td style='$value_style'> ".$new[$i]['value']."</td></tr>";
            }
        }

        $i++;
    }

    $email_body = "<h1 style='margin-bottom: 20px; border-bottom: 1px solid #eee; color: #666'>".$title."</h1><table style='border: 0px; color: #333; width: 100%'>".$email_body."</table>";



    if ($con[0]['mail_type']=='smtp')
    {


        require_once("php/class.phpmailer.php");
        error_reporting(0);


        foreach($rec as $send_to)
        {

            $to = $send_to['val'];

            $mail = new PHPMailer();

            $mail->IsSMTP();
            $mail->Host = $con[0]['smtp_host'];
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->Username = $con[0]['smtp_email'];
            $mail->SetFrom('name@yourdomain.com', 'First Last');

            $mail->Password = $con[0]['smtp_pass'];
            $mail->FromName = $con[0]['smtp_name'];
            $mail->AddAddress($to);
            if ($con[0]['if_ssl']=='ssl')
            {
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
            }

            if ($replyto)
            {
                $mail->AddReplyTo($replyto);
            }
            else
            {
                $mail->AddReplyTo($con[0]['smtp_email']);
            }


            $mail->From = $con[0]['smtp_email'];
            $mail->IsHTML(true);

            if (isset($con[0]['email_sub']))
            {
                if (strpos($con[0]['email_sub'], '{{form_name}}'))
                {
                    $con[0]['email_sub'] = explode("{{form_name}}", $con[0]['email_sub']);
                    $subject = $con[0]['email_sub'][0].$title.$con[0]['email_sub'][1];
                }
                else
                {
                    $subject = $con[0]['email_sub'];
                }
            }
            else
            {
                $subject = "New Submission for '".$title."'";
            }

            $mail->Subject = $subject;
            $mail->Body = $email_body;

            if($mail->Send())
            {
                $success_sent++;
                if ($autoreply)
                {
                   foreach ($autoreply as $ar_to)
                   {
                    $mail->Subject = $con['0']['autoreply_s'];
                    $mail->Body = "<div style='white-space: pre-line'>".$con['0']['autoreply']."</div>";
                    $mail->ClearAddresses();
                    $mail->AddAddress($ar_to);
                    $mail->AddReplyTo($con[0]['smtp_email']);
                    $mail->Send();                    
                }         
            }

        }

    }

}

else
{


    if ($replyto)
    {
        $headers = "From: $sender_name <$sender_email>\r\nReply-To: $replyto\r\n";
    }
    else
    {
        $headers = "From: $sender_name <$sender_email>\r\nReply-To: $sender_email\r\n";
    }

    $headers.= 'MIME-Version: 1.0' . "\r\n";
    $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers.= 'X-Mailer: php' . "\r\n" . "\r\n";

    if (isset($con[0]['email_sub']))
    {
        if (strpos($con[0]['email_sub'], '{{form_name}}'))
        {
            $con[0]['email_sub'] = explode("{{form_name}}", $con[0]['email_sub']);
            $subject = $con[0]['email_sub'][0].$title.$con[0]['email_sub'][1];
        }
        else
        {
            $subject = $con[0]['email_sub'];
        }
    }
    else
    {
        $subject = "New Submission for '".$title."'";
    }


    foreach($rec as $send_to)
    {
     $to = $send_to['val'];
     if (mail($to,$subject,$email_body,$headers))
     {
        $success_sent++;
    }
}

if ($autoreply)
{
    foreach ($autoreply as $ar_to)
    {
        $headers = "From: $sender_name <$sender_email>\r\n".'Reply-To:'.$sender_email."\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $subject = $con['0']['autoreply_s'];
        $message = "<div style='white-space: pre-line'>".$con['0']['autoreply']."</div>";
        $to = $ar_to;
        mail($to,$subject,$message,$headers);
    }
}


}
// ^ End of IF not-SMTP

$new_json = json_encode($new);

global $wpdb, $table_subs, $table_builder, $table_info;


$date = date('d M Y (H:i)');
$date2 = date('Y-m-d');


$temp1 = $wpdb->query( "SELECT * FROM $table_info WHERE time = '$date2' AND id = $id " );

if ($temp1>=1)
{
    $wpdb->query( "UPDATE $table_info SET submissions = submissions + 1 WHERE id = $id  AND time = '$date2' " );
}
else
{
    $temp2 = $wpdb->insert( $table_info, array( 'time' => $date2, 'views' => 0, 'submissions' => 1, 'id' => $id ) );
}


$rows_affected = $wpdb->insert( $table_subs, array( 'content' => $new_json, 'seen' => NULL, 'added' => $date, 'form_id' => $id ) );

$result['done'] = $rows_affected;

    // Display Success Message if Form Submission Updated in DataBase
if($rows_affected)
{
    $error['sent']="true";
    $error['msg']="Message Sent";
    if ( (isset($con[0]['redirect'])) && !(empty($con[0]['redirect'])) )
    {
        $error['redirect']=$con[0]['redirect'];
    }


    $wpdb->query( "UPDATE $table_builder SET
        submits = submits + 1
        WHERE id = '$id'" );


    if (isset($con[0]['success_msg']))
    {
        $error['msg']=$con[0]['success_msg'];
    }

    echo json_encode($error);
}
else
{
    $error['sent']="false";  
    $error['msg']="The message could not be sent";

    if (isset($con[0]['failed_msg']))
    {
        $error['msg']=$con[0]['failed_msg'];
    }

    echo json_encode($error);

}

}
die();
}

function formcraft_email($value, $valid, $req, $min, $max, $tool, $con)
{
    error_reporting(0);
    global $errors;
    $a=0;

    if ( (!(empty($value))) && !(filter_var($value, FILTER_VALIDATE_EMAIL)) )
    {
        if (isset($con['error_email']))
        {
            $errors[$tool][$a] = $con['error_email'];
        }
        else
        {
            $errors[$tool][$a] = 'Incorrect email format.';
        }
        $a++;
    }

}
function formcraft_url($value, $valid, $req, $min, $max, $tool, $con)
{
    error_reporting(0);
    global $errors;
    $a=0;

    if ( (!(empty($value))) && !(filter_var($value, FILTER_VALIDATE_URL)) )
    {

        if (isset($con['error_url']))
        {
            $errors[$tool][$a] = $con['error_url'];
        }
        else
        {
            $errors[$tool][$a] = 'Incorrect URL format.';
        }
        $a++;
    }

}
function formcraft_captcha($value, $valid, $req, $min, $max, $tool, $con)
{
    global $errors;
    global $id;
    $a=0;


    if (isset($_SESSION["security_number_$id"]))
    {
        if ( !($_SESSION["security_number_$id"]==$value) )
        {

            if (isset($con['error_captcha']))
            {
                $errors[$tool][$a] = $con['error_captcha'];
            }
            else
            {
                $errors[$tool][$a] = "Incorrect Captcha";
            }
            $a++;
        }
    }
    else
    {

        if ( !($_SESSION["security_number"]==$value) )
        {

            if (isset($con["error_captcha"]))
            {
                $errors[$tool][$a] = $con["error_captcha"];
            }
            else
            {
                $errors[$tool][$a] = "Incorrect Captcha";
            }
            $a++;
        }

    }


}
function formcraft_integers($value, $valid, $req, $min, $max, $tool, $con)
{
    global $errors;
    $a=0;



    if ( (!(empty($value))) && !(is_numeric($value)) )
    {
        if (isset($con['error_only_integers']))
        {
            $errors[$tool][$a] = $con['error_only_integers'];
        }
        else
        {
            $errors[$tool][$a] = 'Only integers allowed';
        }
        $a++;
    }

}

function formcraft_no_val($value, $req, $min, $max, $tool, $con)
{
    global $errors;
    $a=0;

    if ( ( $req==1 || $req=='true' ) && empty($value) && $value!='0' )
    {
        if (isset($con['error_required']))
        {
            $errors[$tool][$a] = $con['error_required'];
        }
        else
        {
            $errors[$tool][$a] = 'This field is required';
        }
        $a++;
    }
    if ( (!(empty($min))) && (!(empty($value))) && (strlen($value)<$min) )
    {
        if (isset($con['error_min']))
        {
            if (strpbrk($con['error_min'],'{{min_chars}}'))
            {
                $con['error_min'] = explode("{{min_chars}}", $con['error_min'] );
                $errors[$tool][$a] = $con['error_min'][0].$min.$con['error_min'][1];
            }
            else
            {
                $errors[$tool][$a] = $con['error_min'];
            }
        }
        else
        {
            $errors[$tool][$a] = 'At least '.$min.' characers required';
        }
        $a++;
    }
    if ( (!(empty($max))) && (!(empty($value))) && (strlen($value)>$max) )
    {
        if (isset($con['error_max']))
        {
            if (strpbrk($con['error_max'],'{{max_chars}}'))
            {
                $con['error_max'] = explode("{{max_chars}}", $con['error_max'] );
                $errors[$tool][$a] = $con['error_max'][0].$max.$con['error_max'][1];
            }
            else
            {
                $errors[$tool][$a] = $con['error_max'];
            }
        }
        else
        {
            $errors[$tool][$a] = 'At most '.$max.' characers allowed';
        }
        $a++;
    }

}

function formcraft_alphabets($value, $valid, $req, $min, $max, $tool, $con)
{
    global $errors;
    $a=0;

    if ( (!(empty($value))) && !(ctype_alpha($value)) )
    {

        $errors[$tool][$a] = 'Only alphabets allowed';
        $a++;
    }

}

function formcraft_alpha($value, $valid, $req, $min, $max, $tool, $con)
{
    global $errors;
    $a=0;

    if ( (!(empty($value))) && !(ctype_alnum($value)) )
    {

        $errors[$tool][$a] = 'Only alphabets and numbers allowed';
        $a++;
    }

}

function formcraft_update() {


    global $wpdb, $table_subs, $table_builder, $restricted;
    $id = mysql_real_escape_string($_POST['id']);
    $html = mysql_real_escape_string($_POST['content']);
    $build = mysql_real_escape_string($_POST['build']);
    $option = mysql_real_escape_string($_POST['option']);
    $con = mysql_real_escape_string($_POST['con']);
    $recipients = mysql_real_escape_string($_POST['rec']);

    if (array_search($id, $restricted) && $_SERVER['HTTP_HOST'] == 'ncrafts.net')
    {
        die();
    }

    $wpdb->query( "UPDATE $table_builder SET
      build = '$build',
      options = '$option',
      con = '$con',
      recipients = '$recipients',
      html = '$html'
      WHERE id = '$id'" );
    $wpdb->show_errors();

    die();
}
function formcraft_add() {
    global $wpdb, $table_subs, $table_builder;
    
    $_POST['name'] = mysql_real_escape_string($_POST['name']);
    $_POST['desc'] = mysql_real_escape_string($_POST['desc']);

    if (empty($_POST['name']))
    {
        $result2['Error'] = 'Name is required';
        echo json_encode($result2);
        die();
    }
    if (strlen($_POST['name'])<2)
    {
        $result2['Error'] = 'Name is too short';
        echo json_encode($result2);
        die();
    }
    if (strlen($_POST['name'])>90)
    {
        $result2['Error'] = 'Name is too long';
        echo json_encode($result2);
        die();
    }
    if (strlen($_POST['desc'])>500)
    {
        $result2['Error'] = 'Description is too long';
        echo json_encode($result2);
        die();
    }
    if ( (!(empty($_POST['desc']))) && strlen($_POST['desc'])<3)
    {
        $result2['Error'] = 'Description is too short';
        echo json_encode($result2);
        die();
    }

    $dt = date('d M Y (H:i)');

    if ($_POST['type_form']=='duplicate')
    {
        $dup = $_POST['duplicate'];
        
        $dup_id = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id = $dup ", "ARRAY_A" );


        $rows_affected = $wpdb->insert( $table_builder, array( 
            'name' => $_POST['name'], 
            'description' => $_POST['desc'], 
            'html' => $dup_id[0]['html'], 
            'build' => $dup_id[0]['build'], 
            'options' => $dup_id[0]['options'], 
            'con' => $dup_id[0]['con'], 
            'recipients' => $dup_id[0]['recipients'], 
            'added' => $dt 
            ) );

        $result['done'] = $rows_affected;
    }

    else
    {

        $rows_affected = $wpdb->insert( $table_builder, array( 'name' => $_POST['name'], 'description' => $_POST['desc'], 'added' => $dt ) );
        $result['done'] = $rows_affected;

    }

    if($rows_affected)
    {
        $wpdb->query( "SELECT MAX(id) FROM $table_builder", "ARRAY_A" );
        $result2['Added']= $wpdb->insert_id;
        echo json_encode($result2);
    }

    die();
}
function formcraft_del() {
    global $wpdb, $table_subs, $table_builder, $table_info, $restricted;
    $id = $_POST['id'];


    if (array_search($id, $restricted) && $_SERVER['HTTP_HOST'] == 'ncrafts.net')
    {
        die();
    }

    if ($wpdb->query( "DELETE FROM $table_builder WHERE id = '$id'" ))
    {
        if ($wpdb->query( "DELETE FROM $table_info WHERE id = '$id'" ))
        {
            echo "Deleted";
        }
        else
        {
            echo "Deleted";
        }
    }

    die();
}


function formcraft_activate()
{

    error_reporting(0);
    global $wpdb, $table_subs, $table_builder, $table_stats, $table_info;

    if($wpdb->get_var("SHOW TABLES LIKE '$table_builder'") != $table_builder) {
        $post = $_SERVER['SERVER_NAME'];
        $ch = curl_init('http://ncrafts.net/other/domain_count.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($ch);
    }

    $sql = "CREATE TABLE $table_builder (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
      description tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      html MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      build MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      options MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      con MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      recipients text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      added text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
      views INT NOT NULL DEFAULT '0',
      submits INT NOT NULL DEFAULT '0',
      UNIQUE KEY id (id)
      ) CHARACTER SET utf8 COLLATE utf8_general_ci";


require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);


$sql = "CREATE TABLE $table_subs (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  content text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  seen tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  form_id tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  added text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  UNIQUE KEY id (id)
  ) CHARACTER SET utf8 COLLATE utf8_general_ci";

dbDelta($sql);

$sql = "CREATE TABLE $table_stats (
    `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` INT NOT NULL
    ) CHARACTER SET utf8 COLLATE utf8_general_ci";

dbDelta($sql);

$sql = "CREATE TABLE $table_info (
    `time` TEXT NULL,
    `id` INT NULL,
    `views` INT NULL,
    `submissions` INT NULL
    ) CHARACTER SET utf8 COLLATE utf8_general_ci";

dbDelta($sql);

}


register_activation_hook( __FILE__, 'formcraft_activate' );




// formcrafts Usage

function formcrafts_register_scripts () {



    // BootStrap CSS
    wp_enqueue_style( 'bootcss', plugins_url( 'css/bootstrap.css', __FILE__ ));
    wp_enqueue_style( 'facss', plugins_url( 'css/font-awesome/css/font-awesome.min.css', __FILE__ ));


    // jQuery UI CSS
    wp_enqueue_style( 'jQuery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ));

    // Datepicker CSS
    wp_enqueue_style( 'time_style', plugins_url( 'time/css/bootstrap-timepicker.min.css', __FILE__ ));

    
    // CSS for Checkboxes and Radios
    wp_enqueue_style( 'boxes_style', plugins_url( 'css/boxes.css', __FILE__ ));
    wp_enqueue_style( 'ratings_style', plugins_url( 'css/ratings.css', __FILE__ ));

    // Main Form CSS
    wp_enqueue_style( 'main_style', plugins_url( 'css/nform_style.css', __FILE__ ));





    // jQuery
    wp_enqueue_script('jquery');

    // Datepicker
    wp_enqueue_script( 'js_lib', plugins_url( 'libraries/js_libraries.js', __FILE__ ));
    
    // Basic JS to handle forms
    wp_enqueue_script( 'formcraftjs', plugins_url( 'js/form.js', __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-mouse', 'jquery-ui-widget', 'jquery-ui-sortable', 'jquery-ui-slider'));
    wp_localize_script( 'formcraftjs', 'FormCraftJS', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'server' => plugins_url('/formcraft/file-upload/server/php/'), 'locale' => plugins_url('/formcraft/datepicker/js/locales/'), 'other' => plugins_url('/formcraft') ) );

    wp_enqueue_script( 'formcraftjs2', plugins_url( 'js/form_only.js', __FILE__ ));


    // File Upload JS
    wp_enqueue_script( 'upload-iframe-js', plugins_url( 'file-upload/js/jquery.iframe-transport.js', __FILE__ ));

    // BootStrap
    wp_enqueue_script( 'bootjs', plugins_url( 'bootstrap/js/bootstrap.min.js', __FILE__ ));



    

}



class formcraft_Widget extends WP_Widget
{
  function formcraft_Widget()
  {
    $widget_ops = array('classname' => 'formcraft_Widget', 'description' => 'Use a form');
    $this->WP_Widget('formcraft_Widget', 'FormCraft', $widget_ops);
}

function form($instance)
{
    $instance = wp_parse_args((array) $instance, array( 'form_id' => '', 'text' => '' ));
    $form_id = $instance['form_id'];
    $text = stripslashes($instance['text']);
    ?>

    <p><em style='color: #666'>Select a form which you wish to use as a widget. The form will open in a modal box on clicking a link.</em>
    </p>

    <?php 
    global $wpdb, $table_subs, $table_builder;
    if (!empty($form_id))
    {


      $myrows = $wpdb->get_results( "SELECT name FROM $table_builder WHERE id=$form_id", "ARRAY_A" );

  }
  echo "Current Text: ".stripslashes($instance['text'])."<br>";
  if (!empty($myrows[0]['name']))
  {
    echo "Current Form: ".$myrows[0]['name'];
}
else
{
    echo "Current Form: (none)";
}
echo "<div style='border-bottom: 1px solid #EEE; margin: 10px 2px;'></div>";
$text;
?>
<label for='<?php echo $this->get_field_id('text'); ?>'>Link Text, or HTML:</label>
<textarea style='width: 222px' id="<?php echo $this->get_field_id('text'); ?>"  name="<?php echo $this->get_field_name('text'); ?>"><?php echo stripslashes($instance['text']); ?></textarea>

<p><label for="<?php echo $this->get_field_id('form_id'); ?>">Form Name:</label>
    <select style='width: 150px' id="<?php echo $this->get_field_id('form_id'); ?>"  name="<?php echo $this->get_field_name('form_id'); ?>">
        <option value=''></option>
        <?php

        global $wpdb, $table_subs, $table_builder;

        $myrows = $wpdb->get_results( "SELECT * FROM $table_builder" );
        foreach ($myrows as $row) {
            echo "<option value='$row->id'>$row->name</option>";
        }


        ?>
    </select>
</p>
<?php
echo "<div style='margin: 13px 2px;'></div>";

}

function update($new_instance, $old_instance)
{
    $instance = $old_instance;
    $instance['form_id'] = $new_instance['form_id'];
    $instance['text'] = addslashes(htmlentities($new_instance['text']));
    return $instance;
}

function widget($args, $instance)
{
    extract($args, EXTR_SKIP);


    // Load Scripts and CSS
    formcrafts_register_scripts();

    global $wpdb, $table_subs, $table_builder;
    $id = $instance['form_id'];


    if (!(empty($id)))
    {

        $myrows = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id=$id" );
        foreach ($myrows as $row) {
            $temp_css = json_decode(stripslashes($row->con), true);
            $css = $temp_css[0]['custom_css'];
        }


        $temp_2 = stripslashes(html_entity_decode($instance['text']));

        $temp = '

        <a href="#myModal'.$id.'" role="button" data-toggle="modal" id="'.$id.'_a" onClick="javascript:increment_form(this.id)" class="modal_trigger">'.$temp_2.'</a>

        <div id="myModal'.$id.'" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
            <button type="button" class="close modal_close"    aria-hidden="true">×</button>
            <div>';

                foreach ($myrows as $row) {
                    $temp.= stripslashes($row->html);
                }

                $temp.='
            </div>
        </div>';

        echo "<style>$css</style>".$temp;

    }


}
}

add_action( 'widgets_init', create_function('', 'return register_widget("formcraft_Widget");') );

add_shortcode( 'formcraft', 'add_formcraft' );

function add_formcraft( $atts, $content = null ){

    extract( shortcode_atts( array(
        'id' => '1',
        'type' => '',
        'opened' => '0',
        'class' => 'btn',
        'background' => '#eee',
        'text_color' => '#333'
        ), $atts ) );



    // Load Scripts and CSS
    formcrafts_register_scripts();


    global $wpdb, $table_subs, $table_builder, $table_stats;
    
    $myrows = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id=$id" );

    foreach ($myrows as $row) {
        $temp_css = json_decode(stripslashes($row->con), true);
        $css = $temp_css[0]['custom_css'];
    }

    if ($type=='popup')
    {

        $temp = "<div class='bootstrap'>
        <a href='#myModal".$id."' role='button' data-toggle='modal' id='".$id."_a' onClick='javascript:increment_form(this.id)' class='".$class." modal_trigger'>".$content."</a>

        <div id='myModal".$id."' class='modal hide' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='display: none'>
            <button type='button' class='close modal_close' aria-hidden='true'>×</button>
            <div></div>";

            foreach ($myrows as $row) {
                $temp.= stripslashes($row->html);
            }

            $temp.= "
        </div>
    </div>";

    return "<style>$css</style>".$temp;

}
elseif ($type=='sticky') {

    if ($opened)
    {
        $class = 'sticky_cover open';
    }
    else
    {
        $class = 'sticky_cover';
    }

    $temp = "
    <div id='nform_sticky' class='".$class." bootstrap'>
        <span id='".$id."_a'  title='Open/Close' onClick='javascript:increment_form(this.id)' class='sticky_toggle' style='background-color: $background; color: $text_color'>".$content." <i class='icon-angle-up'></i></span>
        <div class='sticky_nform'>";

            foreach ($myrows as $row) {
                $temp.= stripslashes($row->html);
            }

            $temp.= "
        </div>
    </div>";

    return "<style>$css</style>".$temp;

}
elseif ($type=='fly') {

    if ($opened)
    {
        $class = 'fly_cover open';
    }
    else
    {
        $class = 'fly_cover';
    }

    $temp = "
    <div id='nform_fly' class='".$class." bootstrap'>
        <span id='".$id."_a' title='Open/Close' onClick='javascript:increment_form(this.id)' class='fly_toggle' style='background-color: $background; color: $text_color'>".$content."</span>
        <div class='fly_form'>
            <span id='".$id."_a' class='close modal_close' >×</span>
            ";

            foreach ($myrows as $row) {
                $temp.= stripslashes($row->html);
            }

            $temp.= "
        </div>
    </div>";

    return "<style>$css</style>".$temp;

}
else 
{

    $wpdb->query( "UPDATE $table_builder SET
        views = views + 1
        WHERE id = '$id'" );

    $insert = $wpdb->insert( $table_stats, array( 
        'id' => $id
        ) );

    formcraft_increment($id);



    foreach ($myrows as $row) 
    {
        return "<style>$css</style>".stripslashes($row->html);
    }
}

}





function formcraft( $id, $type = '', $opened = '0', $text = 'Click Here', $class = '', $background = '#eee', $text_color = '#333' ){

    // Load Scripts and CSS
    formcrafts_register_scripts();

    global $wpdb, $table_subs, $table_builder, $table_stats;
    
    $myrows = $wpdb->get_results( "SELECT * FROM $table_builder WHERE id=$id" );

    $wpdb->query( "UPDATE $table_builder SET
        views = views + 1
        WHERE id = '$id'" );

    foreach ($myrows as $row) {
        $temp_css = json_decode(stripslashes($row->con), true);
        $css = $temp_css[0]['custom_css'];
    }

    if ($type=='popup')
    {
        $temp= '
        <a href="#myModal'.$id.'" role="button" data-toggle="modal" id="'.$id.'_a" onClick="javascript:increment_form(this.id)" class="'.$class.' modal_trigger">'.$text.'</a>

        <!-- Modal -->
        <div id="myModal'.$id.'" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
            <button type="button" class="close modal_close"    aria-hidden="true">×</button>
            <div>
                ';

                foreach ($myrows as $row) {
                    $temp.= stripslashes($row->html);
                }

                $temp.='
            </div>
        </div>';

        echo "<style>$css</style>".$temp;


    }
    elseif ($type=='sticky') {

        if ($opened)
        {
            $class = 'sticky_cover open';
        }
        else
        {
            $class = 'sticky_cover';
        }


        $temp = "

        <div id='nform_sticky' class='".$class." bootstrap'>

            <span id='".$id."_a' onClick='javascript:increment_form(this.id)' class='sticky_toggle' style='background-color: $background; color: $text_color'>".$text." <i class='icon-angle-up'></i></span>


            <div class='sticky_nform'>";

                foreach ($myrows as $row) {
                    $temp.= stripslashes($row->html);
                }

                $temp.= "
            </div>
        </div>";

        echo "<style>$css</style>".$temp;

    }
    else 
    {

        $wpdb->query( "UPDATE $table_builder SET
            views = views + 1
            WHERE id = '$id'" );

        formcraft_increment($id);

        foreach ($myrows as $row) {
            echo "<style>$css</style>".stripslashes($row->html);
        }
    }

}

if ( $_SERVER['HTTP_HOST'] == 'ncrafts.net'  )
{
    add_action( 'admin_init', 'stop_access_profile' );
}
function stop_access_profile() {
    remove_menu_page( 'profile.php' );
    remove_menu_page( 'dashboard.php' );
    remove_submenu_page( 'users.php', 'profile.php' );
    if(IS_PROFILE_PAGE === true) {
        wp_die( 'You are not permitted to change your own profile information. Please contact a member of HR to have your profile information changed.' );
    }
}




wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-ui-sortable');


add_action( 'admin_menu', 'formcraft_menu' );
function formcraft_menu() {
    if ( $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'ncrafts.net'  )
    {
        add_menu_page( 'FormCraft - Form Builder', 'FormCraft', 'read', 'survey_builder', 'formcraft_menu_options', plugins_url('formcraft/images/icon.png' ),'31.21' );
    }
    else
    {
        add_menu_page( 'FormCraft - Form Builder', 'FormCraft', 'remove_users', 'survey_builder', 'formcraft_menu_options', plugins_url('formcraft/images/icon.png' ),'31.21' );
    }
}
function formcraft_menu_options() {


    if (isset($_GET['id']))
    {
        $url = plugins_url();
        $to_include = 'builder.php';
    }
    else
    {
        $to_include='index2.php';
    }

    require($to_include);

}


?>