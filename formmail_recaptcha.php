<?php
/* Copyright (c) 2002 Eli Sand */

	########################################################################
	#                                                                      #
	#                      PHP FormMail v2.0 20030305                      #
	#                                                                      #
	########################################################################
	#
	# Settings
	#

	# Initialize variables
	#
	$auth = NULL;
	$deny = NULL;
	$must = NULL;
	$post = NULL;
	$http = NULL;
	$form = NULL;
	$list = NULL;

	# Fix for pre PHP 4.1.x versions
	#
	if (!isset($_POST)) {
		$_POST = &$HTTP_POST_VARS;
	}
	if (!isset($_SERVER)) {
		$_SERVER = &$HTTP_SERVER_VARS;
	}
	if (!function_exists('array_key_exists')) {
		function array_key_exists($key, $array) {return in_array($key, array_keys($array)) ? true : false;}
	}

	# Fix for magic quotes when enabled
	#
	if (get_magic_quotes_gpc()) {
		foreach ($_POST as $key => $value) {
			$_POST["$key"] = stripslashes($value);
		}
	}

	# Detect any Windows operating system
	#
	if (strstr(php_uname(), 'Windows')) {
		$IS_WINDOWS = TRUE;
	}
	
	# Get the referring domain name
	#
	$referer_domain = get_domain_referer($_SERVER['HTTP_REFERER']);

	########################################################################
	#                                                                      #
	#                      USER CONFIGURABLE SETTINGS                      #
	#                                                                      #
	########################################################################

	# Authorized email address masks that can be used as the recipient
	#
	$auth = "*@127.0.0.1, *@localhost";
	$auth .= ",*@mychurchserver.com,*@naturecoastdesign.net,";

	# Authorize all email addresses to the current domain
	#
	# If you want strict email account authorization, comment this out and
	# the script will only authorize the masks in the list defined above.
	#
	$auth .= ", *@" . get_domain($_SERVER['SERVER_NAME']);

	# Email address masks that will be rejected if in the email field
	#
	$deny = "nobody@*, anonymous@*, postmaster@*";
	
	# Authorized domain referers
	#
	$auth_domains = "mychurchserver.com,naturecoastdesign.net";


	# The following allow you to set some default settings
	#
	# These are commented out by default and when used, either override or
	# append to any values.  This allows you to ensure that hackers don't
	# post their own values to certain fields, making you miss out on
	# important data that you want to ensure is included in the email.
	#
	#$must['required']                = "env_report";
	$must['env_report']              = "SERVER_NAME,HTTP_REFERER,REMOTE_ADDR";
	#$must['redirect']                = "http://my.domain.com/ok.html";
	#$must['error_redirect']          = "http://my.domain.com/error.html";
	#$must['missing_fields_redirect'] = "http://my.domain.com/missing.html";

	#
	########################################################################

	########################################################################
	#                                                                      #
	#                 DO NOT EDIT ANYTHING PAST THIS POINT                 #
	#                                                                      #
	########################################################################

	########################################################################
	#
	# Functions
	#
	
	#Function to validate against any email injection attempts
	function IsInjected($str)
	{
	  $injections = array('(\n+)', '(\r+)', 'to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:','(\t+)','(%0A+)','(%0D+)','(%08+)','(%09+)');
	  $inject = join('|', $injections);
	  $inject = "/$inject/i";
	  if(preg_match($inject,$str))
		{
		return true;
	  }
	  else
		{
		return false;
	  }
	}
	
	#Function to validate against any email injection attempts
	function Isnumber()
	{
	  // Break up the string:
      $phone = preg_replace("/[^0-9]/", "", $_POST['phone'] );
	  if( is_numeric($phone) && strlen($phone) == '10')
	  {
		return true;
	  }
	  else
	  {
		return false;
	  }
	}
	
	if ($error_msg != NULL) {
		echo '<p class="error">ERROR: '. nl2br($error_msg) . "</p>";
	}
	if ($result != NULL) {
		echo '<p class="success">'. $result . "</p>";
		echo $post['answer'];
	}

	# Trim leading and trailing white space from array values
	#
	function array_trim(&$value, $key) {
		$value = trim($value);
	}

	# Return the top level domain of a hostname
	#
	function get_domain($string) {
		if (eregi('\.?([a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]+)$', $string, $values)) {
			return $values[1];
		}

		return NULL;
	}
	
	# Return the top level domain of a referer
	#
	function get_domain_referer($string) {
    $referer_domain = parse_url($string);
    return get_domain($referer_domain['host']);
  }

	# Show an error message to the user
	#
	function error_msg($error, $required = FALSE) {
		global $post;

		if (!empty($post['missing_fields_redirect']) && $required) {
			header('Location: ' . $post['missing_fields_redirect']);
		}
		elseif (!empty($post['error_redirect'])) {
			header('Location: ' . $post['error_redirect']);
		}
		else {
			echo "<html>\r\n";
			echo "\t<head>\r\n";
			echo "\t\t<title>Form Error</title>\r\n";
			echo "\t\t<style type=\"text/css\">* {font-family: \"Verdana\", \"Arial\", \"Helvetica\", monospace;}</style>\r\n";
			echo "\t</head>\r\n";
			echo "\t<body>\r\n";
			echo "\t\t<p>${error}</p>\r\n\t\t<p><small>&laquo; <a href=\"javascript: history.back();\">go back</a></small></p>\r\n";
			echo "\t</body>\r\n";
			echo "</html>\r\n";
		}

		exit();
	}

	# Basic pattern matching on an entire array
	#
	function pattern_grep($input, $array) {
		foreach ($array as $value) {
			$value = addcslashes($value, '^.[]$()|{}\\');
			$value = str_replace('*', '.*', $value);
			$value = str_replace('?', '.?', $value);
			$value = str_replace('+', '.+', $value);

			if (eregi('^' . $value . '$', $input)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	#
	########################################################################

	########################################################################
	#
	# Main
	#

	# Check to make sure the info was posted
	#
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	# Check to make sure it is an acceptable referer
	#
	if (stristr($auth_domains,$referer_domain)) {

		$post = array(
			'answer'			=> $_POST['answer'],
			'recipient'			=> $_POST['recipient'],
			'email'				=> $_POST['email'],
			'subject'			=> $_POST['subject'],
			'realname'			=> $_POST['realname'],
			'required'			=> $_POST['required'],
			'env_report'			=> $_POST['env_report'],
			'sort'				=> $_POST['sort'],
			'redirect'			=> $_POST['redirect'],
			'error_redirect'		=> $_POST['error_redirect'],
			'missing_fields_redirect'	=> $_POST['missing_fields_redirect']
		);

		$http = array(
			'REMOTE_USER'			=> $_SERVER['REMOTE_USER'],
			'REMOTE_ADDR'			=> $_SERVER['REMOTE_ADDR'],
			'HTTP_USER_AGENT'		=> $_SERVER['HTTP_USER_AGENT'],
			'HTTP_REFERER'		=> get_domain_referer($_SERVER['HTTP_REFERER']),
			'SERVER_NAME'			=> $_SERVER['SERVER_NAME']
		);

		if (isset($must['required'])) {
			$post['required']			= $must['required'] . ',' . $_POST['required'];
		}
		if (isset($must['env_report'])) {
			$post['env_report']			= $must['env_report'] . ',' . $_POST['env_report'];
		}
		if (isset($must['redirect'])) {
			$post['redirect']			= $must['redirect'];
		}
		if (isset($must['error_redirect'])) {
			$post['error_redirect']			= $must['error_redirect'];
		}
		if (isset($must['missing_fields_redirect'])) {
			$post['missing_fields_redirect']	= $must['missing_fields_redirect'];
		}

		if (($auth = explode(',', $auth))) {
			array_walk($auth, 'array_trim');
		}
		if (($deny = explode(',', $deny))) {
			array_walk($deny, 'array_trim');
		}

    # Check if reCaptcha is required
    #
    if (!empty($post['required']) && stristr($post['required'],'answer')) {
    # Check for reCaptcha response
    #
    	if (strtolower($post['answer']) !== "green") {
      	error_msg("The answer wasn't entered correctly. Go back and try it again.");
		}
    }

		# Check for missing required fields
		#
		if ((!empty($post['required'])) && ($list = explode(',', $post['required']))) {
			$list[] = 'recipient';
			$list[] = 'email';

			array_walk($list, 'array_trim');

			foreach ($list as $value) {
				if (!empty($value) && empty($_POST["$value"])) {
					error_msg("You have left a required field ($value) blank.", TRUE);
				}
			}
		}

		# Check the email addresses submitted
		#
		if (pattern_grep($post['email'], $deny)) {
			error_msg("You have specified a banned email address.");
		}
		if (!eregi('^([a-zA-Z0-9\.\_\-]+)\@((([a-zA-Z0-9\-]+)\.)+([a-zA-Z]+))$', $post['email'])) {
			error_msg("You have specified an invalid email address.");
		}
		if (!$IS_WINDOWS) {
			if (!getmxrr(get_domain($post['email']), $mxhost)) {
				error_msg("You have no mail exchange records for your email address.");
			}
		}

		# Check if the recipients email address is authorized
		#
		if ((!empty($post['recipient'])) && ($list = explode(',', $post['recipient']))) {
			array_walk($list, 'array_trim');

			foreach ($list as $value) {
				if (!eregi('^([a-zA-Z0-9\.\_\-]+)\@((([a-zA-Z0-9\-]+)\.)+([a-zA-Z]+))$', $value)) {
					error_msg("The recipients email address is invalid.");
				}
				if (!pattern_grep($value, $auth)) {
					error_msg("The recipients email address is unauthorized.");
				}
			}
		}
		else {
			error_msg("There was an unknown error while checking the recipients email address.");
		}
		
		# Check if the posted email Is Injected 
		#
		if( IsInjected($_POST['email']) )
		{
			error_msg("Bad email value!");
		}
		# Check if the posted phone number is a number 
		#
		if( !Isnumber() )
		{
			error_msg("the phone number you entered is not vaild");
		}


		# Sort the fields
		#
		if ((!empty($post['sort'])) && ($list = explode(',', $post['sort']))) {
			array_walk($list, 'array_trim');

			foreach ($list as $value) {
				$form["$value"] = $_POST["$value"];
			}
		}
		else {
			$form = $_POST;
		}

		# Create the message
		#
		$subject = empty($post['subject']) ? "Online form" : "Online form: " . $post['subject'];

		$message = "Submitted by: " . $post['realname'] . " <" . $post['email'] . "> on " . date('l, F jS, Y @ g:i:s a (O)') . "\r\n\r\n";

		$message .= "Online Form Fields\r\n";
		$message .= "------------------\r\n";

		foreach ($form as $key => $value) {
			if (!array_key_exists($key, $post)) {
        $message .= "${key}: ${value}\r\n";
			}
		}

		if (!empty($post['env_report'])) {
			if (($list = explode(',', $post['env_report']))) {
				$message .= "Client Variables\r\n";
				$message .= "----------------\r\n";

				array_walk($list, 'array_trim');

				foreach ($list as $value) {
					if (array_key_exists($value, $http)) {
						$message .= "${value}: " . $http["$value"] . "\r\n";
					}
				}
			}
		}

		# End of message
    $message .= "------------------\r\n";

		# Send out the email
		#
		if (mail($post['recipient'], $subject, $message, "From: " . $post['email'] . "\r\nReply-To: " . $post['email'] . "\r\nX-Mailer: PHP FormMail")) {

			if (!empty($post['redirect'])) {
				header('Location: ' . $post['redirect']);
			}
			else {
				echo "<html>\r\n";
				echo "\t<head>\r\n";
				echo "\t\t<title>Thank you</title>\r\n";
				echo "\t\t<style type=\"text/css\">* {font-family: \"Verdana\", \"Arial\", \"Helvetica\", monospace;}</style>\r\n";
				echo "\t</head>\r\n";
				echo "\t<body>\r\n";
				echo "\t\t<p>Thank you for filling out the form.</p>\r\n\t\t<p><small>&laquo; <a href=\"javascript: history.back();\">go back</a></small></p>\r\n";
				echo "\t</body>\r\n";
				echo "</html>\r\n";
			}
		}
		else {
			error_msg("There was an unknown error while sending email.");
		}
	}
	else {
		error_msg("This domain is unauthorized to use this program.");
	}
	}
	else {
		error_msg("Invalid request method used.");
	}

	#
	########################################################################
?>
