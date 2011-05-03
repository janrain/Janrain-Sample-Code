<?php
ob_start();
session_name('engage');
session_start();
$session = $_SESSION;
if (@is_array($session['authinfo']['accessCredentials'])) {
	unset($session['authinfo']['accessCredentials']);
}
if ( empty($session['authinfo']['profile']['identifier']) ) {
  $session = array('stat' => 'fail');
}else{
  $session['stat'] = 'ok';
}
ob_end_clean();
echo json_encode($session);
exit;
?>
