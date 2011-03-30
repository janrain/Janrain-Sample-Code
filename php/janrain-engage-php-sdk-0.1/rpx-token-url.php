<?php
/**
 *Below is a very simple PHP 5 script that implements an Engage token URL to collect and output the results from auth_info.
 *The code below assumes you have the CURL HTTP fetching library with SSL and PHP JSON support.
 */
ob_start();
require_once('engage.lib.php');
$debug_array = array('Debug out:');

/**
 *For a production script it would be better to include (require_once) the apiKey in from a file outside the web root to enhance security.
 */
$api_key = 'YOUR API KEY HERE';//<- API KEY HERE

$token = $_POST['token'];
$format = ENGAGE_FORMAT_JSON;
$extended = true;

$result = engage_auth_info($api_key, $token, $format, $extended);
if ($result === false) {
	$errors = engage_get_errors();
	foreach ($errors as $error=>$label) {
		$debug_array[] = 'Error: '.$error;
	}
} else {
	$array_out = true;
/* On a successful authentication the variable (array) $auth_info_array will contain the resulting data. */
	$auth_info_array = engage_parse_result($result, $format, $array_out);
	$debug_array = print_r($auth_info_array, true);
}

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
		<title>Janrain Engage token URL example</title>
	</head>
	<body>
		<pre>
<?php echo $the_debug; ?>
		</pre>
	</body>
</html>
