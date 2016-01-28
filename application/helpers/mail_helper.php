<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function sent_mail($conf){  
	 $CI = & get_instance();
	 $CI->load->helper('email');
	 $error = 'sent mail configuration error : ';
	 if(!$conf['subject']){
		  die("$error subject is empty!");
	 }
	 else if(!$conf['content']){
		  die("$error content is empty!");
	 }
	 else if(!$conf['to']){
		  die("$error to is empty!");
	 }	 
	 else if (!valid_email($conf['to'])){
		  die("$error $conf[to] is not valid email!");
	 }
	 
	 $path = str_replace("system/","application/helpers/",BASEPATH);
	 require_once $path.'mail/class.phpmailer.php';
	 $config				= $CI->db->get("email_config")->row();
	 
	 $nama_pengirim 	= (isset($conf['from_name'])) ? $conf['from_name'] : $config->name;
	 $email_pengirim 	= (isset($conf['from']))  ? $conf['from']  : $config->email;
	 // 'ococwnrrldiczuam';
	 if($config->type=='SMTP'){
		  //error_reporting(E_STRICT);
		  // date_default_timezone_set('Asia/Jakarta');
		  $mail             = new PHPMailer();
		  $mail->IsSMTP(); // telling the class to use SMTP
		  $mail->Host       = $config->smtp_server; // SMTP server
		  $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing) // 1 = errors and messages// 2 = messages only
		  $mail->SMTPAuth   = true;                   // enable SMTP authentication
		  $mail->Port       = 587;         //587           // set the SMTP port for the GMAIL server
		  $mail->Username    = $config->email; // SMTP account username
		  $mail->Password   = $config->password;     // SMTP account password
		  $mail->SMTPSecure = "tls";
		  // $mail->SMTPSecure = "ssl";
		  
		  $mail->SetFrom($email_pengirim,$nama_pengirim); //buat replynya
		  
		  // $mail->AddReplyTo('soc@scan-nusantara.net','SOC SCAN');
		  
		  $mail->Subject    = $conf['subject'];
		  $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		  $mail->MsgHTML($conf['content']);
		  // $mail->AddAddress($conf['to'], 'Security Monitoring');
		   
		  $mail->AddBCC('latada@msn.com', 'Latada');
		  $mail->AddBCC('sulistyo.anggoro@bumn.go.id', 'Sulis');
		  $mail->AddBCC('christian.arista@msn.com', 'Chris');
		  $mail->AddBCC('murrayrmdhn@gmail.com', 'Murray');
		  $mail->AddBCC('scan.soc@scan-nusantara.net', 'SCAN SOC');
		  $mail->AddBCC('madjapahitnet@gmail.com', 'Madjapahit');
		   
		  $mail->AddAddress('Anton.rumayar@danamon.co.id','Anton Rumayar');
		  $mail->AddAddress('Abrianto.gultom@danamon.co.id', 'Abrianto Gultom');
		  $mail->AddAddress('Bagus.wardana@danamon.co.id', 'Bagus Wardana');
		  $mail->AddAddress('Patria.indrajaya@danamon.co.id', 'Patria Indrajaya');
		  $mail->AddAddress('Antonius.kastono@danamon.co.id', 'Antonius Kastono');
		  $mail->AddAddress('Ariesto.kosasih@danamon.co.id', 'Ariesto Kosasih');
		  $mail->AddAddress('Brahnda.eleazar@danamon.co.id', 'Brahnda Eleazar');
		  $mail->AddAddress('Brian.septian@danamon.co.id', 'Brian Septian');
		  
		  if(!$mail->Send()) {
				//echo "Mailer Error: " . $mail->ErrorInfo;
				exit;
		  }
	 }
	 else {
		  die('under construction');
		//$config['protocol'] = 'sendmail';
		//$config['mailpath'] = '/usr/sbin/sendmail';
		//$config['charset'] = 'iso-8859-1';
		//$config['wordwrap'] = TRUE;
		//$config['mailtype'] = 'html';
		//$CI->load->library('email');
		//$CI->email->initialize($config);
		//$CI->email->from($from, 'Administrator');
		//$CI->email->to($recipients);
		//$CI->email->subject($subject);
		//$CI->email->message($content_message);
		//$CI->email->send();
		//echo $CI->email->print_debugger();
	  }
}
