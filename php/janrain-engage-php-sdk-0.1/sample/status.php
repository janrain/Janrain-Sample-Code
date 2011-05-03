<?php
ob_start();
session_name('engage');
session_start();
if ( !empty($_SESSION['user_data']) ) {
	$stat = ok;
} else {
	$stat = 'fail';
}
ob_end_clean();
echo json_encode(array('user_data'=>$_SESSION['user_data'],'stat'=>$stat));
exit;
?>
