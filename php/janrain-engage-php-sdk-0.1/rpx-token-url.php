<?php
/**
 *Below is a very simple PHP 5 script that implements an Engage token URL to collect and output the results from auth_info.
 *The code below assumes you have the CURL HTTP fetching library with SSL and PHP JSON support.
 */

require_once('engage.lib.php');
$the_output = '';

/**
 *For a production script it would be better to include the apiKey in from a file outside the web root to enhance security.
 */
$api_key = 'YOUR API KEY HERE';//<- API KEY HERE

$token = $_POST['token'];
$format = ENGAGE_FORMAT_JSON;
$extended = true;

$result = engage_auth_info($api_key, $token, $format, $extended);
if ($result === false) {
	$errors = engage_get_errors();
	foreach ($errors as $error=>$label) {
		$the_output .= '<br />Error: '.$error;
	}
} else {
	$array_out = true;
	$auth_info_array = engage_parse_result($result, $format, $array_out);
	$the_output .= print_r($auth_info_array, true);
}

?>
<html>
	<head>
		<title>Janrain Engage token URL example</title>
	</head>
	<body>
		<pre>
<?php echo $the_output; ?>
		</pre>
	</body>
</html>

