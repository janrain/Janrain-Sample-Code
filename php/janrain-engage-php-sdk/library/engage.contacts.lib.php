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

if (defined('ENGAGE_LIB_INIT')) {
  if (ENGAGE_LIB_INIT === true) {
  engage_define('ENGAGE_GETCONTACTS_EP', 'get_contacts');
  engage_define('ENGAGE_GETCONTACTS_PROVIDERS', 'Google,Yahoo,Windows Live,Facebook,MySpace,Twitter,LinkedIn');
  } else {
    return;
  }
} else {
  return;
}

/* begin engage_get_contacts */
/**
 * http://documentation.janrain.com/engage/api/get_contacts
 * To use get_contacts requires a subscription level of Pro or better.
 * It is not recommended to use API call as part of sign in.
 * Users with large numbers of friends will notice the delay.
 * Setup an asynchronous call to collect this (e.g. iframe or server-side script).
 */
function engage_get_contacts($api_key, $identifier, $format=ENGAGE_FORMAT_JSON) {
  $ready = true;
  if (strlen($api_key) != ENGAGE_API_KEY_LEN) {
    engage_error(ENGAGE_ERROR_APIKEY, __FUNCTION__);
    $ready = false;
  }
  if (empty($identifier)) {
    engage_error(ENGAGE_ERROR_IDENT, __FUNCTION__);
    $ready = false;
  }
  if (!in_array($format, explode(',',ENGAGE_FORMATS))) {
    engage_error(ENGAGE_ERROR_FORMAT, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $url = ENGAGE_API_BASE_URL.ENGAGE_GETCONTACTS_EP;
    $parameters = array(
      ENGAGE_KEY_APIKEY => $api_key,
      ENGAGE_KEY_IDENTIFIER => $identifier,
      ENGAGE_KEY_FORMAT => $format
    );
    $result = engage_post($url, $parameters);
    return $result;
  }
  return false;
}
/* end engage_get_contacts */

/* begin engage_get_contacts_provider */
/**
 * Test if provider is valid for get_contacts
 */
function engage_get_contacts_provider($provider) {
  $provider_array = explode(',', ENGAGE_GETCONTACTS_PROVIDERS);
  if (in_array($provider, $provider_array)) {
    return true;
  }
  return false;
}
/* end engage_get_contacts_provider */

?>
