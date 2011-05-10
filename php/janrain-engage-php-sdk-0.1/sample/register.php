<?php
ob_start();
$user_fields = array();
$user_fields['user_name'] = '';
$user_fields['first_name'] = '';
$user_fields['last_name'] = '';
$user_fields['email'] = '';
$user_fields['profile_url'] = '';
$user_fields['phone'] = '';
$user_fields['company'] = '';
session_name('engage');
session_start();
$incoming_nonce = strip_tags(urldecode($_GET['nonce']));
if ($incoming_nonce == $_SESSION['nonce'] && !empty($_SESSION['nonce'])) {
	$_SESSION['nonce'] = '';
	foreach ($user_fields as $key=>$var){
		if ( !empty($_GET[$key]) ) {
			$user_fields[$key] = strip_tags(urldecode($_GET[$key]));
		}
	}
	$stat = 'ok';
	$_SESSION['user_data'] = $user_fields;
} else {
	$stat = 'fail';
	$_SESSION['user_data'] = '';
}
ob_end_clean();
echo json_encode(array('stat'=>$stat));
exit;
?>
