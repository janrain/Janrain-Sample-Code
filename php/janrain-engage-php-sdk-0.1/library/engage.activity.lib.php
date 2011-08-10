<?PHP

//init
engage_define('ENGAGE_ACTIVITY_EP', 'activity');
engage_define('ENGAGE_ACT_MAX_IMAGE_COUNT', 5);
engage_define('ENGAGE_ACT_MAX_FLASH_COUNT', 1);
engage_define('ENGAGE_ACT_MAX_MP3_COUNT', 1);
engage_define('ENGAGE_ACT_MIN_FLASH_WIDTH', 30);
engage_define('ENGAGE_ACT_MAX_FLASH_WIDTH', 90);
engage_define('ENGAGE_ACT_MIN_FLASH_EWIDTH', 1);
engage_define('ENGAGE_ACT_MAX_FLASH_EWIDTH', 398);

engage_define('ENGAGE_ACTVITYTRUNCATE', 'true');
engage_define('ENGAGE_URLSHORTENING', 'true');

engage_define('ENGAGE_ACTIVITY_PROVIDERS', 'LinkedIn,Twitter,Facebook,Yahoo!,MySpace');

engage_define('ENGAGE_KEY_ACTIVITY', 'activity');
engage_define('ENGAGE_KEY_TRUNCATE', 'truncate');
engage_define('ENGAGE_KEY_LOCATION', 'location');
engage_define('ENGAGE_KEY_URLSHORTENING', 'url_shortening');

engage_define('ENGAGE_ACT_KEY_MEDIA', 'media');
engage_define('ENGAGE_ACT_KEY_URL', 'url');
engage_define('ENGAGE_ACT_KEY_SRC', 'src');
engage_define('ENGAGE_ACT_KEY_HREF', 'href');
engage_define('ENGAGE_ACT_KEY_TYPE', 'type');
engage_define('ENGAGE_ACT_KEY_TITLE', 'title');
engage_define('ENGAGE_ACT_KEY_ALBUM', 'album');
engage_define('ENGAGE_ACT_KEY_WIDTH', 'width');
engage_define('ENGAGE_ACT_KEY_HEIGHT', 'height');
engage_define('ENGAGE_ACT_KEY_EWIDTH', 'expanded_width');
engage_define('ENGAGE_ACT_KEY_EHEIGHT', 'expanded_height');
engage_define('ENGAGE_ACT_KEY_SWFSRC', 'swfsrc');
engage_define('ENGAGE_ACT_KEY_IMGSRC', 'imgsrc');
engage_define('ENGAGE_ACT_KEY_ACTION', 'action');
engage_define('ENGAGE_ACT_KEY_ARTIST', 'artist');
engage_define('ENGAGE_ACT_KEY_PROPERTIES', 'properties');
engage_define('ENGAGE_ACT_KEY_USERCONTENT', 'user_generated_content');
engage_define('ENGAGE_ACT_KEY_ACTIONLINKS', 'action_links');
engage_define('ENGAGE_ACT_KEY_DESCRIPTION', 'description');

engage_define('ENGAGE_ACT_TYPE_IMAGE', 'image');
engage_define('ENGAGE_ACT_TYPE_FLASH', 'flash');
engage_define('ENGAGE_ACT_TYPE_MP3', 'mp3');

/* begin engage_activity */
/**
 * http://documentation.janrain.com/activity
 * To use activity requires a subscription level of Pro or better.
 *
 * You must setup the provider(s) for sharing on the Engage dashboard.
 * (rpxnow.com - Deployment - Configure Providers)
 *
 * The following fields are only used by Facebook and are ignored by other providers:
 * title, description, action_links, media, properties
 *
 * Read more about the Facebook extras at the URL below.
 * http://developers.facebook.com/docs/guides/attachments
 *
 * If more than one media type is included the "media" array Facebook will 
 * choose only one of these types. This is the order Facebook will use to select: 
 * image, flash, mp3 (a.k.a. music)
 */
function engage_activity($api_key, $identifier, $activity, $truncate = ENGAGE_ACTVITYTRUNCATE, $url_shortening = ENGAGE_URLSHORTENING, $location = NULL) {
  $ready = true;
  if (strlen($api_key) != ENGAGE_API_KEY_LEN) {
    engage_error(ENGAGE_API_KEY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (empty($identifier)) {
    engage_error(ENGAGE_IDENTIFIER_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (!is_array($activity)) {
    engage_error(ENGAGE_ARRAY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $url = ENGAGE_API_BASE_URL.ENGAGE_ACTIVITY_EP;
    $activity = json_encode($activity);
    $parameters = array(
      ENGAGE_KEY_APIKEY => $api_key,
      ENGAGE_KEY_IDENTIFIER => $identifier,
      ENGAGE_KEY_ACTIVITY => $activity,
      ENGAGE_KEY_TRUNCATE => $truncate,
      ENGAGE_KEY_URLSHORTENING => $url_shortening
    );
    if ($location !== NULL) {
      $parameters[ENGAGE_KEY_LOCATION] = $location;
    }
    $result = engage_post($url, $parameters);
    return $result;
  }
  return false;
}
/* end engage_activity */

/* begin engage_activity_item */
function engage_activity_item($base, $media=NULL, $action_links=NULL, $properties=NULL) {
  $ready = true;
  if (!is_array($base)) {
    engage_error(ENGAGE_ARRAY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $activity = $base;
    $activity[ENGAGE_ACT_KEY_MEDIA] = $media;
    $activity[ENGAGE_ACT_KEY_ACTIONLINKS] = $action_links;
    $activity[ENGAGE_ACT_KEY_PROPERTIES] = $properties;
    return $activity;
  }
  return false;
}
/* end engage_activity_item */

/* begin engage_activity_base */
function engage_activity_base($url, $action, $user_content=NULL, $title=NULL, $description=NULL) {
  $ready = true;
  if (!is_string($url) || !is_string($action)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $base = array (
      ENGAGE_ACT_KEY_URL    => $url,
      ENGAGE_ACT_KEY_ACTION => $action,
    );
    if ($user_content !== NULL) {
      $base[ENGAGE_ACT_KEY_USERCONTENT] = $user_content;
    }
    if ($title !== NULL) {
      $base[ENGAGE_ACT_KEY_TITLE] = $title;
    }
    if ($description !== NULL) {
      $base[ENGAGE_ACT_KEY_DESCRIPTION] = $description;
    }
    return $base;
  }
  return false;
}
/* end enage_activity_base */

/* begin engage_activity_media_image */
function engage_activity_media_image($src_url, $href_url, $media_image=NULL) {
  $ready = true;
  if (!is_string($src_url) || !is_string($href_url)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $image_array = array();
    if (is_array($media_image)) {
      if (count($media_image) < ENGAGE_ACT_MAX_IMAGE_COUNT) {
        $image_array = $media_image;
      } else {
        engage_error(ENGAGE_COUNT_ERROR, __FUNCTION__, ENGAGE_ETYPE_DEBUG);
        return $media_image;
      }
    }
    $image_array[] = array(
      ENGAGE_ACT_KEY_TYPE => ENGAGE_ACT_TYPE_IMAGE,
      ENGAGE_ACT_KEY_SRC  => ENGAGE_ACT_TYPE_IMAGE,
      ENGAGE_ACT_KEY_HREF => ENGAGE_ACT_TYPE_IMAGE
    );
    return $image_array;
  }
  return false;
}
/* end engage_activity_media_image */

/* begin engage_activity_media_flash */
function engage_activity_media_flash($swf_url, $thumb_url, $width, $height, $ewidth, $eheight, $media_flash=NULL) {
  $ready = true;
  if (!is_string($swf_url) || !is_string($thumb_url)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (!is_int($width) || !is_int($height) || !is_int($ewidth) || !is_int($eheight)) {
    enagage_error(ENGAGE_INT_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (ENGAGE_ACT_MIN_FLASH_WIDTH <= $width && $width <= ENGAGE_ACT_MAX_FLASH_WIDTH) {
    engage_error(ENGAGE_RANGE_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (ENGAGE_ACT_MIN_FLASH_HEIGHT <= $height && $height <= ENGAGE_ACT_MAX_FLASH_HEIGHT) {
    engage_error(ENGAGE_RANGE_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (ENGAGE_ACT_MIN_FLASH_EWIDTH <= $ewidth && $ewidth <= ENGAGE_ACT_MAX_FLASH_EWIDTH) {
    engage_error(ENGAGE_RANGE_ERROR, __FUNCTION__);
    $ready = false;
  }
  if (ENGAGE_ACT_MIN_FLASH_EHEIGHT <= $eheight && $eheight <= ENGAGE_ACT_MAX_FLASH_EHEIGHT) {
    engage_error(ENGAGE_RANGE_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $flash_array = array();
    if (is_array($media_flash)) {
      if (count($media_flash) < ENGAGE_ACT_MAX_FLASH_COUNT) {
        $flash_array = $media_flash;
      } else {
        engage_error(ENGAGE_COUNT_ERROR, __FUNCTION__, ENGAGE_ETYPE_DEBUG);
        return $media_flash;
      }
    }
    $flash_array[] = array(
      ENGAGE_ACT_KEY_TYPE => ENGAGE_ACT_TYPE_FLASH,
      ENGAGE_ACT_KEY_SWFSRC  => $swf_url,
      ENGAGE_ACT_KEY_IMGSRC => $thumb_url,
      ENGAGE_ACT_KEY_WIDTH => $width,
      ENGAGE_ACT_KEY_HEIGHT => $height,
      ENGAGE_ACT_KEY_EWIDTH => $ewidth,
      ENGAGE_ACT_KEY_EHEIGHT => $eheight
    );
    return $flash_array;
  }
  return false;
}
/* end engage_activity_media_flash */

/* begin engage_activity_media_mp3 */
function engage_activity_media_mp3($mp3_url, $title, $artist, $album, $media_mp3=NULL) {
  $ready = true;
  if (!is_string($mp3_url) || !is_string($title) || !is_string($artist) || !is_string($album)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $mp3_array = array();
    if (is_array($media_mp3)) {
      if (count($media_mp3) < ENGAGE_ACT_MAX_MP3_COUNT) {
        $mp3_array = $media_mp3;
      } else {
        engage_error(ENGAGE_COUNT_ERROR, __FUNCTION__, ENGAGE_ETYPE_DEBUG);
        return $media_mp3;
      }
    }
    $mp3_array[] = array(
      ENGAGE_ACT_KEY_TYPE => ENGAGE_ACT_TYPE_MP3,
      ENGAGE_ACT_KEY_SRC  => $mp3_url,
      ENGAGE_ACT_KEY_TITLE => $title,
      ENGAGE_ACT_KEY_ARTIST => $artist,
      ENGAGE_ACT_KEY_ALBUM => $album
    );
    return $mp3_array;
  }
  return false;
}
/* end engage_activity_media_mp3 */

/* begin engage_activity_action_link */
function engage_activity_action_link($action_url, $action_text) {
  $ready = true;
  if (!is_string($action_url) || !is_string($action_text)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true) {
    $action_link = array(
      array(
        ENGAGE_ACT_KEY_TITLE => $action_text,
        ENGAGE_ACT_KEY_HREF => $action_url
      )
    );
    return $action_link;
  }
  return false;
}
/* end engage_activity_action_link */

/* begin engage_activity_properties */
function engage_activity_properties($properties_array) {
  $ready = true;
  if (!is_array($properties_array) || empty($properties_array)) {
    engage_error(ENGAGE_ARRAY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true) {
    return $properties_array;
  }
  return false;
}
/* end engage_activity_properties */

/* begin engage_activity_provider */
/**
 * Test if provider is valid for activity
 */
function engage_activity_provider($provider) {
  $provider_array = explode(',', ENGAGE_ACTIVITY_PROVIDERS);
  if (in_array($provider, $provider_array)) {
    return true;
  }
  return false;
}
/* end engage_activity_provider */
?>
