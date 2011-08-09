<?PHP

//init
engage_define('ENGAGE_ACTIVITY_EP', 'activity');

engage_define('ENGAGE_ACTVITYTRUNCATE', 'true');
engage_define('ENGAGE_URLSHORTENING', 'true');

engage_define('ENGAGE_ACTIVITY_PROVIDERS', 'LinkedIn,Twitter,Facebook,Yahoo!,MySpace');

engage_define('ENGAGE_KEY_ACTIVITY', 'activity');
engage_define('ENGAGE_KEY_TRUNCATE', 'truncate');
engage_define('ENGAGE_KEY_URL_SHORTENING', 'url_shortening');
engage_define('ENGAGE_KEY_LOCATION', 'location');

engage_define('ENGAGE_ACT_KEY_MEDIA', 'media');
engage_define('ENGAGE_ACT_KEY_ACTIONLINKS', 'action_links');
engage_define('ENGAGE_ACT_KEY_PROPERTIES', 'properties');
engage_define('ENGAGE_ACT_KEY_URL', 'url');
engage_define('ENGAGE_ACT_KEY_ACTION', 'action');
engage_define('ENGAGE_ACT_KEY_USERCONTENT', 'user_generated_content');
engage_define('ENGAGE_ACT_KEY_TITLE', 'title');
engage_define('ENGAGE_ACT_KEY_DESCRIPTION', 'description');

engage_define('ENGAGE_ARRAY_ERROR', 'array expected');
engage_define('ENGAGE_STRING_ERROR', 'string expected');

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
function engage_activity_item($basic, $media=NULL, $action_links=NULL, $properties=NULL) {
  $ready = true;
  if (!is_array($basic)) {
    engage_error(ENGAGE_ARRAY_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $activity = $basic;
    $activity[ENGAGE_ACT_KEY_MEDIA] = $media;
    $activity[ENGAGE_ACT_KEY_ACTIONLINKS] = $action_links;
    $activity[ENGAGE_ACT_KEY_PROPERTIES] = $properties;
    return $activity;
  }
  return false;
}
/* end engage_activity_item */

/* begin engage_activity_basic */
function engage_activity_basic($url, $action, $user_content=NULL, $title=NULL, $description=NULL) {
  $ready = true;
  if (!is_string($url) & !is_string($action)) {
    engage_error(ENGAGE_STRING_ERROR, __FUNCTION__);
    $ready = false;
  }
  if ($ready === true){
    $basic = array (
      ENGAGE_ACT_KEY_URL    => $url,
      ENGAGE_ACT_KEY_ACTION => $action,
    );
    if ($user_content !== NULL) {
      $basic[ENGAGE_ACT_KEY_USERCONTENT] = $user_content;
    }
    if ($title !== NULL) {
      $basic[ENGAGE_ACT_KEY_TITLE] = $title;
    }
    if ($description !== NULL) {
      $basic[ENGAGE_ACT_KEY_DESCRIPTION] = $description;
    }
    return $basic;
  }
  return false;
}
/* end enage_activity_basic */

/* begin engage_activity_media */
function engage_activity_media() {
      $media = array(
        array( 'type' => 'image', 
               'src' => 'http://docj27ko03fnu.cloudfront.net/rel/img/861d564d23ba416d9b480deac7c9f1f6.png', 
               'href' => 'http://plugins.janrain.com/wordpress/'
        )
      );

      $media = array(
        array( 'type' => 'flash', 
               'swfsrc' => 'http://www.adobe.com/swf/software/flash/about/flash_animation.swf', 
               'imgsrc' => 'http://wwwimages.adobe.com/www.adobe.com/ubi/template/identity/adobe/screen/SiteHeader/logo.png',
               'width' => '90',/*width and height must be between 30 and 90 inclusive*/
               'height' => '90',
               'expanded_width' => '398',/*expanded width and height must be 398 or less*/
               'expanded_height' => '98'
        )
      );

      $media = array(                            /*multi-dimensional array or object*/
        array( 'type' => 'mp3',/*this is sometimes documented as "music", use "mp3"*/ 
               'src' => 'http://ontherecordpodcast.com/pr/otro/electronic/Get_Facebook_Friends_and_Twitter_Followers_While_You_Sleep.mp3', 
               'title' => 'Get Facebook Friends and Twitter Followers While You Sleep',
               'artist' => 'Tore Steen',
               'album' => 'On The Record Online'
        )
      );
}
/* end engage_activity_media */

/* begin engage_activity_action_links */
function engage_activity_action_links() {
      $action_links = array(
        array( 'text' => 'action link text.', 'href' => 'https://support.janrain.com/' )
      );
}
/* end engage_activity_action_links */

/* begin engage_activity_properties */
function engage_activity_properties() {
  $properties = array(                            /*multi-dimensional array or object*/
        'Potatoes' => 'mashed',
        'Apples'   => array( 'text' => 'property link', 'href' => 'http://www.apple.com/' )
  );
}
/* end engage_activity_properties */
?>
