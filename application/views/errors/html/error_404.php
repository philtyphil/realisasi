<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Login to - eKoders Responsive Admin Theme</title>
	
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="<?php echo config_item('base_url');?>assets/ekoders/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo config_item('base_url');?>assets/ekoders/css/fonts.css">
	<link rel="stylesheet" href="<?php echo config_item('base_url');?>assets/ekoders/font-awesome/css/font-awesome.min.css">
	
	<!-- PAGE LEVEL PLUGINS STYLES -->	

    <!-- Tc core CSS -->
	<link id="qstyle" rel="stylesheet" href="<?php echo config_item('base_url');?>assets/ekoders/css/themes/style.css">
	
	
    <!-- Add custom CSS here -->

	<!-- End custom CSS here -->
	
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
	
  </head>

  <body class="error">
	<div id="wrapper">
		<!-- BEGIN MAIN PAGE CONTENT -->
		<div class="error-container">
			<div class="container">
				<div class="error-box">
					<h1 class="error-code"><i class="fa fa-warning smaller-50"></i> 404 <small>Page Not Found</small></h1>
					<h3><?php echo $message; ?></h3>
								
					<div class="space-12"></div>
								
					<a href="<?php echo config_item('base_url');?>home" class="btn btn-primary">Go to Home!</a>
				</div>
			</div>
		</div>
		<!-- END MAIN PAGE CONTENT --> 
	</div> 
	 
    <!-- core JavaScript -->
    <script src="<?php echo config_item('base_url');?>assets/ekoders/js/jquery.min.js"></script>
    <script src="<?php echo config_item('base_url');?>assets/ekoders/js/bootstrap.min.js"></script>
	
	<!-- PAGE LEVEL PLUGINS JS -->
	
    <!-- Themes Core Scripts -->	
	
	<!-- initial page level scripts for examples -->
  </body>
</html>