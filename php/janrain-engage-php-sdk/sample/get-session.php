<?php
ob_start();
session_name('engage');
session_start();
$session = $_SESSION['authinfo'];
if (@is_array($session['accessCredentials'])) {
	unset($session['accessCredentials']);
}
ob_end_clean();
echo json_encode($session);
exit;
?>
