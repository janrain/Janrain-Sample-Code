<?php
ob_start();
$stat = 'ok';
require('conf.php');
if ( empty($application_domain) || empty($app_id) || empty($api_key) ) {
  $stat = 'fail';
}
$application_domain = rtrim($application_domain,'/ ');
$settings = array(
 'application_domain' => $application_domain,
 'app_id' => $app_id
 );
ob_end_clean();
echo json_encode(array('settings'=>$settings,'stat'=>$stat));
exit;
?>
