<?php
/**
 * Copyright 2011
 * Janrain Inc.
 * All rights reserved.
 */
/**
 * Requires engage.api.lib.php
 */
if ( !defined('ENGAGE_LIB_INIT') ) {
  require_once('engage.api.lib.php');
}

/* begin engage_auth_info */
/**
 * http://documentation.janrain.com/engage/api/auth_info
 * Extended requires subscription level of Plus or better.
 */
function engage_auth_info($api_key, $token, $format=ENGAGE_FORMAT_JSON, $extended=ENGAGE_AUTH_EXTEND) {
  if ($extended === true) {
    $extended = 'true';
  } else {
    $extended = 'false';
  }
  $ready = true;
  if (strlen($api_key) != ENGAGE_API_KEY_LEN) {
    engage_error(ENGAGE_ERROR_APIKEY, __FUNCTION__);
    $ready = false;
  }
  if (strlen($token) != ENGAGE_TOKEN_LEN) {
    engage_error(ENGAGE_ERROR_TOKEN, __FUNCTION__);
    $ready = false;
  }
  if (!in_array($format, explode(',',ENGAGE_FORMATS))) {
    engage_error(ENGAGE_ERROR_FORMAT, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $url = ENGAGE_API_BASE_URL.ENGAGE_AUTHINFO_EP;
    $parameters = array(
      ENGAGE_KEY_APIKEY => $api_key,
      ENGAGE_KEY_TOKEN => $token,
      ENGAGE_KEY_FORMAT => $format,
      ENGAGE_KEY_EXTEND => $extended
    );
    $result = engage_post($url, $parameters);
    return $result;
  }
  return false;
}
/* end engage_auth_info */

?>
