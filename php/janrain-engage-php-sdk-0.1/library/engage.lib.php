<?php
/**
 * Copyright 2011
 * Janrain Inc.
 * All rights reserved.
 */

/* run engage_lib_init */
engage_define('ENGAGE_LIB_DEVMODE', false);
engage_lib_init(ENGAGE_LIB_DEVMODE);

/* begin engage_lib_init */
function engage_lib_init($dev_mode=false) {
  if (defined('ENGAGE_LIB_INIT')) {
    if (ENGAGE_LIB_INIT === true) {
      return;
    }
  }
  engage_define('ENGAGE_DEV_MODE', $dev_mode);
  engage_define('ENGAGE_API_KEY_LEN', 40);
  engage_define('ENGAGE_TOKEN_LEN', 40);
  engage_define('ENGAGE_POST_SSL', true);
  engage_define('ENGAGE_PARSE_ARRAY', true);
  engage_define('ENGAGE_AUTH_EXTEND', false);
  engage_define('ENGAGE_FORMAT_JSON', 'json');
  engage_define('ENGAGE_FORMAT_XML', 'xml');
  engage_define('ENGAGE_FORMATS', ENGAGE_FORMAT_JSON.','.ENGAGE_FORMAT_XML);
  engage_define('ENGAGE_KEY_APIKEY', 'apiKey');
  engage_define('ENGAGE_KEY_TOKEN', 'token');
  engage_define('ENGAGE_KEY_FORMAT', 'format');
  engage_define('ENGAGE_KEY_EXTEND', 'extended');
  engage_define('ENGAGE_KEY_IDENTIFIER', 'identifier');
  engage_define('ENGAGE_API_BASE_URL', 'https://rpxnow.com/api/v2/');
  engage_define('ENGAGE_AUTHINFO_EP', 'auth_info');
  engage_define('ENGAGE_GETCONTACTS_EP', 'get_contacts');
  engage_define('ENGAGE_GETCONTACTS_PROVIDERS', 'Google,Yahoo,Windows Live,Facebook,MySpace,Twitter,LinkedIn');
  engage_define('ENGAGE_ELABEL_DEBUG', 'debug');
  engage_define('ENGAGE_ELABEL_MESSAGE', 'message');
  engage_define('ENGAGE_ELABEL_WARN', 'warning');
  engage_define('ENGAGE_ELABEL_ERROR', 'error');
  engage_define('ENGAGE_API_KEY_ERROR', 'invalid api key');
  engage_define('ENGAGE_TOKEN_ERROR', 'invalid token');
  engage_define('ENGAGE_FORMAT_ERROR', 'invalid format');
  engage_define('ENGAGE_JSON_ERROR', 'json decode error');
  engage_define('ENGAGE_JERROR_DEPTH', ', maximum stack depth exceeded');
  engage_define('ENGAGE_JERROR_CHAR', ', unexpected character found');
  engage_define('ENGAGE_JERROR_SYN', ', malformed JSON');
  engage_define('ENGAGE_XML_ERROR', 'XML error code:');
  engage_define('ENGAGE_IDENTIFIER_ERROR', 'missing identifier');
  if (ENGAGE_DEV_MODE === true) {
    if (!version_compare(PHP_VERSION, '5.0.0', '>=')){
      engage_error('PHP version less than required version', __FUNCTION__);
    }
    if (!function_exists('json_decode')) {
      engage_error('JSON library not found', __FUNCTION__);
    }
    if (!function_exists('curl_init')) {
      engage_error('cURL libary not found', __FUNCTION__);
    }
    if (!function_exists('simplexml_load_string')) {
      engage_error('XML library not found', __FUNCTION__);
    }
  }
  define('ENGAGE_LIB_INIT', true);
}
/* end engage_lib_init */

/* begin engage_define */
function engage_define($label, $value) {
  if (!defined($label)) {
    define($label, $value);
  }
}
/* end engage_define */

/* begin engage_parse_result */
function engage_parse_result($result, $format, $array_out=ENGAGE_PARSE_ARRAY) {
  if ($array_out === true) {
    $array = true;
  } else {
    $array = false;
  }
  $ready = true;
  if ($result === false) {
    $ready = false;
  }
  if (!in_array($format, explode(',',ENGAGE_FORMATS))) {
    $ready = false;
  }
  if ($ready === true) {
    if ($format == ENGAGE_FORMAT_JSON) {
      $decode_result = json_decode($result, $array);
      if ($decode_result === NULL) {
        switch(json_last_error()) {
          case JSON_ERROR_DEPTH:
              $json_error = ENGAGE_JERROR_DEPTH;
          break;
          case JSON_ERROR_UTF8:
          case JSON_ERROR_CTRL_CHAR:
              $json_error = ENGAGE_JERROR_CHAR;
          break;
          case JSON_ERROR_SYNTAX:
          case JSON_ERROR_STATE_MISMATCH:
              $json_error = ENGAGE_JERROR_SYN;
          break;
        }
        engage_error(ENGAGE_JSON_ERROR.$json_error, __FUNCTION__);
        return false;
      }
    } elseif ($format == ENGAGE_FORMAT_XML) {
      $xmlconfig = libxml_use_internal_errors(true);
      $decode_result = simplexml_load_string($result);
      if ($decode_result === false) {
        $xml_errors = libxml_get_errors();
        foreach ($xml_errors as $xml_error) {
          engage_error(ENGAGE_XML_ERROR.$xml_error->code, __FUNCTION__);
        }
        libxml_clear_errors();
        return false;
      }
      if ($array === true) {
        $decode_result = json_encode($decode_result);
        $decode_result = engage_parse_result($decode_result, ENGAGE_FORMAT_JSON, true);
      }
    }
    return $decode_result;
  }
  return false;
}
/* end engage_parse_result */

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
    engage_error(ENGAGE_API_KEY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (strlen($token) != ENGAGE_TOKEN_LEN) {
    engage_error(ENGAGE_TOKEN_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (!in_array($format, explode(',',ENGAGE_FORMATS))) {
    engage_error(ENGAGE_FORMAT_ERROR, __FUNCTION__);
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
    engage_error(ENGAGE_API_KEY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (empty($identifier)) {
    engage_error(ENGAGE_IDENTIFIER_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (!in_array($format, explode(',',ENGAGE_FORMATS))) {
    engage_error(ENGAGE_FORMAT_ERROR, __FUNCTION__);
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

/* begin engage_post */
function engage_post($url, $parameters, $ssl=ENGAGE_POST_SSL) {
  $curl = curl_init();
  if ($curl == false) {
    engage_error(ENGAGE_CURL_ERROR, __FUNCTION__);
    return false;
  }
  engage_error('parameters: ' . print_r($parameters, true), __FUNCTION__, ENGAGE_ELABEL_DEBUG);  
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  $result = curl_exec($curl);
  if ($result == false) {
    engage_error('Curl error: ' . curl_error($curl), __FUNCTION__);
    engage_error('HTTP code: ' . curl_errno($curl), __FUNCTION__);
    engage_error('parameters: ' . print_r($parameters, true), __FUNCTION__);
    curl_close($curl);
  } else {
    curl_close($curl);
    return $result;
  }
  return false;
}
/* end engage_post */

/* begin engage_error */
function engage_error($error, $function='unknown', $label=ENGAGE_ELABEL_ERROR){
  global $engage_errors;
  if (!is_array($engage_errors)){
    $engage_errors = array();
  }
  $error_key = $function.':'.$error;
  $engage_errors[$error_key] = $label;
}
/* end engage_error */

/* begin engage_get_errors */
function engage_get_errors($label=NULL) {
  global $engage_errors;
  $return_errors = array();
  if ($label === NULL) {
  $return_errors =  $engage_errors;
  } else {
    foreach($engage_errors as $key=>$val) {
      if ($label == $val) {
        $return_errors[$key] = $val;
      }
    }
  }
  if (!empty($return_errors)){
    return $return_errors;
  }
  return false;
}
/* end engage_get_errors */

?>
