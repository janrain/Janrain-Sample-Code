<?php
/**
 * Copyright 2011
 * Janrain Inc.
 * All rights reserved.
 */
/**
 * Below is a very simple PHP 5 script that 
 * implements an Engage token URL to collect 
 * and output the results from auth_info.
 * The code below assumes you have the 
 * CURL HTTP fetching library with SSL and 
 * PHP JSON support.
 */

ob_start();
require_once('../library/engage.auth.lib.php');
$debug_array = array('Debug out:');

/**
 * For a production script it would be better 
 * to include (require_once) the apiKey in 
 * from a file outside the web root to 
 * enhance security.
 * 
 * Set your API key (secret) in this file.
 * The varable is $api_key
 */
require_once('engage-conf.php');

$token = $_POST['token'];
$format = ENGAGE_FORMAT_JSON;
$extended = $auth_info_extended;

$result = engage_auth_info($api_key, $token, $format, $extended);
if ($result === false) {
	$errors = engage_get_errors();
	foreach ($errors as $error=>$label) {
		$debug_array[] = 'Error: '.$error;
	}
} else {
/**
 * On a successful authentication store
 * the auth_info data in the variable
 * $auth_info_array
 */
	$array_out = true;
	$auth_info_array = engage_parse_result($result, $format, $array_out);
        //Put a printed copy in the debug.
	$debug_array[] = print_r($auth_info_array, true);
/**
 * This is the point to add code to do something with the Engage data.
 */
}

$errors = engage_get_errors(ENGAGE_ELABEL_ERROR);
foreach ($errors as $error=>$label) {
	$error_array[] = 'Error: '.$error;
}

/*
 * Uncomment lines below to get SDK level
 * debug data. Caution: This could result in 
 * revealing the api_key.
 */
//$debugs = engage_get_errors(ENGAGE_ELABEL_DEBUG);
//foreach ($debugs as $debug=>$label) {
//	$debug_array[] = 'Debug: '.$debug;
//}

$the_buffer = ob_get_contents();
if (!empty($the_buffer)) {
	$debug_array[] = 'Buffer: '.$the_buffer;
}
/* The variable (string) $the_debug will contain debug data. */
$the_debug = implode("\n", $debug_array);
$the_error = implode("\n", $error_array);
ob_end_clean();
?>
<html>
	<head>
		<title>Janrain Engage token URL example</title>
	</head>
	<body>
		<pre>
<?php echo $the_error; ?>

<?php echo $the_debug; ?>
		</pre>
	</body>
</html>
