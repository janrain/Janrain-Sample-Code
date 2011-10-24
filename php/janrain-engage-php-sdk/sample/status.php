<?php
ob_start();
$stat = 'ok';
session_name('engage');
session_start();
if ( !empty($_SESSION['authinfo']) ){
  require('insert-update-user.php');
  if ( empty($_SESSION['user_data']) && !empty($user_data) && $stat == 'ok') {
    $_SESSION['user_data'] = $user_data;
  }
  if ( !empty($map_data) ) {
    $_SESSION['map_data'] = $map_data;
  }
}
$stat = 'fail';
if ( !empty($_SESSION['user_data']) ) {
  if ( !empty($_SESSION['user_data']['user_name']) ){
    $stat = 'ok';
  }
}
ob_end_clean();
echo json_encode(array('user_data'=>$_SESSION['user_data'],'stat'=>$stat));
exit;
?>
