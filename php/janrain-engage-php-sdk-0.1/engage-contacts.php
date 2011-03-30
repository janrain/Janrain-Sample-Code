<?php
/**
 * Copyright 2011
 * Janrain Inc.
 * All rights reserved.
 */

ob_start();

require_once('engage.lib.php');
$debug_array = array('Debug out:');

/**
 *For a production script it would be better to include (require_once) the apiKey in from a file outside the web root to enhance security.
 */
require_once('engage-conf.php');//<- Set your API KEY in the variable $api_key in this file.

$identifier = urldecode($_GET['identifier']);// Get the identifier from the HTTP query.
$format = ENGAGE_FORMAT_JSON;

$result = engage_get_contacts($api_key, $identifier);
if ($result === false) {
	$errors = engage_get_errors();
	foreach ($errors as $error=>$label) {
		$debug_array[] = 'Error: '.$error;
	}
} else {
	$array_out = true;
/* On a successful get_contacts the variable (array) $get_contacts_array will contain the resulting data. */
	$get_contacts_array = engage_parse_result($result, $format, $array_out);
	$debug_array[] = print_r($get_contacts_array, true);
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
ob_end_clean();
?>
<html>
	<head>
		<title>Janrain Engage get_contacts example</title>
	</head>
	<body>
		<pre>
<?php echo $the_debug; ?>
		</pre>
	</body>
</html>
