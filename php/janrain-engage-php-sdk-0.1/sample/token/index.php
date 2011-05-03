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
require_once('../../engage.lib.php');
$debug_array = array('Debug out:');

/**
 * For a production script it would be better
 * to include (require_once) the apiKey in
 * from a file outside the web root to
 * enhance security.
 *
 * Set your API key (secret) in this file.
 * The varable is $api_key
 *
 * Set the "Pro" status in this file.
 * The variable is $engage_pro
 */
require_once('engage-conf.php');

$token = $_POST['token'];
$format = ENGAGE_FORMAT_JSON;
$extended = false;

$result = engage_auth_info($api_key, $token, $format, $extended);
if ($result === false) {
  $errors = engage_get_errors();
  foreach ($errors as $error=>$label) {
    $debug_array[] = 'Error: '.$error;
  }
} else {
  $array_out = true;
/**
 * On a successful authentication the
 * variable (array) $auth_info_array
 * will contain the resulting data.
 */
  $auth_info_array = engage_parse_result($result, $format, $array_out);
  $debug_array[] = print_r($auth_info_array, true);
}

/* Can we use get_contacts? */
$go_contacts = false;
if ($engage_pro === true) {
  if (is_array($auth_info_array)) {
    if (engage_get_contacts_provider($auth_info_array['profile']['providerName'])) {
      $go_contacts = true;
    }
  }
}

if (is_array($auth_info_array)) {
  //Pull the "likes"
  $url = 'https://graph.facebook.com/'.$auth_info_array['accessCredentials']['uid'].'?access_token='.urlencode($auth_info_array['accessCredentials']['accessToken']);
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  $result = curl_exec($curl);
  if ($result == false){
    $debug_array[] = "\n".'URL:'.$url;
    $debug_array[] = "\n".'Curl error: ' . curl_error($curl);
    $debug_array[] = "\n".'HTTP code: ' . curl_errno($curl);
    $debug_array[] = "\n"; print_r($post_data, true);
  } else {
    $graph_likes = json_decode($result);
  }
  curl_close($curl);
}

/**
 * Uncomment lines below to get SDK level
 * debug data. Caution: This could result in
 * revealing the api_key.
 */
//$debugs = engage_get_errors(ENGAGE_ELABEL_DEBUG);
//foreach ($debugs as $debug=>$label) {
//  $debug_array[] = 'Debug: '.$debug;
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
    <title>Janrain Engage token URL example</title>
  </head>
  <body>
<?php
/**
 * For this get_contacts sample to work you
 * need to set $engage_pro to true.
 */
if ($go_contacts === true) {
?>
    <h4>get_contacts</h4>
    <p>Loaded in an iframe with a trigger link to allow the parent page to render while this loads.</p>
    <iframe src="engage-contacts.php?identifier=<?php
    echo urlencode($auth_info_array['profile']['identifier']);
    ?>" style="width:100%;height:240px"></iframe>
<?php
}
if (is_array($auth_info_array)) {
  session_name('engage');
  session_start();
  $_SESSION['authinfo'] = $auth_info_array;
  if ( !empty($graph_likes) ) {
    $_SESSION['graph_likes'] = $graph_likes;
  }    
}
?>
    <pre>
<?php //echo $the_debug; ?>
    </pre>
    <script type="text/javascript">
<?php if ($_GET['custom_ui'] == 'true') { ?>
      window.opener.parent.authDone(false);
      window.opener.checkProviderCookie();
      self.close();
<?php } else { ?>
      self.parent.authDone(true);
<?php } ?>
    </script>
  </body>
</html>
