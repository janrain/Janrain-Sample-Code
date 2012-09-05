<?php
/*
 * This script is intended as an educational tool.
 * Please look at the PHP SDK if you are looking for somthing suited to a new project.
 * https://github.com/janrain/Janrain-Sample-Code/tree/master/php/janrain-engage-php-sdk
 */

ob_start();
/*
 Below is a very simple and verbose PHP 5 script that implements the Engage token URL processing and some popular Pro/Enterprise examples.
 The code below assumes you have the CURL HTTP fetching library with SSL.  
*/

// PATH_TO_API_KEY_FILE should contain a path to a plain text file containing only
// your API key. This file should exist in a path that can be read by your web server,
// but not publicly accessible to the Internet.
$rpx_api_key = trim( file_get_contents( "PATH_TO_API_KEY_FILE" ) );

/*
 Set this to true if your application is Pro or Enterprise.
 Set this to false if your application is Basic or Plus.
*/
$engage_pro = false;

/* STEP 1: Extract token POST parameter */
$token = $_POST['token'];

//Some output to help debugging
echo "SERVER VARIABLES:\n";
var_dump($_SERVER);
echo "HTTP POST ARRAY:\n";
var_dump($_POST);

if(strlen($token) == 40) {//test the length of the token; it should be 40 characters

  /* STEP 2: Use the token to make the auth_info API call */
  $post_data = array('token'  => $token,
                     'apiKey' => $rpx_api_key,
                     'format' => 'json',
                     'extended' => 'true'); //Extended is not available to Basic.

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  $result = curl_exec($curl);
  if ($result == false){
    echo "\n".'Curl error: ' . curl_error($curl);
    echo "\n".'HTTP code: ' . curl_errno($curl);
    echo "\n"; var_dump($post_data);
  }
  curl_close($curl);


  /* STEP 3: Parse the JSON auth_info response */
  $auth_info = json_decode($result, true);

  if ($auth_info['stat'] == 'ok') {
    echo "\n auth_info:";
    echo "\n"; var_dump($auth_info);

    /* Pro API examples */
    /* Basic and Plus please skip down to Step 4 */
    if ($engage_pro === true){

      /* Extract the needed variables from the response */
      $profile = $auth_info['profile'];
      $identifier = $profile['identifier'];
      $provider = $profile['providerName'];

      /* Example of the get_contacts API */
      //Only run if the provider is one supported for get_contacts
      if ($provider == 'Google' || $provider == 'Twitter' || $provider == 'Facebook' || $provider == 'Yahoo!'){
        $post_data = array(
          'apiKey' => $rpx_api_key,
          'identifier' => $identifier
        );
        $url = 'https://rpxnow.com/api/v2/get_contacts';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $result = curl_exec($curl);
        if ($result == false){
          echo "\n".'URL:'.$url;
          echo "\n".'Curl error: ' . curl_error($curl);
          echo "\n".'HTTP code: ' . curl_errno($curl);
          echo "\n"; var_dump($post_data);
        }
        $get_contacts = json_decode($result);
        curl_close($curl);
        echo "\n get_contacts:";
        echo "\n"; var_dump($get_contacts);
      }

/* 
      activity api (Pro)

      Notes:
      This script does not check if you have setup the required application
      on rpxnow.com (Configure Providers) to support social publishing.

      The following fields are only used by Facebook and are ignored by other providers:
        title 
        description
        action_links
        media
        properties

      Read more about the Facebook extras at the URL below.
      http://developers.facebook.com/docs/guides/attachments

      If you include more than one media type in the "media" array, 
      Facebook will choose only one of these types, in this order: 
        1. image
        2. flash
        3. mp3 (a.k.a. music)
*/
      //Only run if the provider is one supported for activity
      if ($provider == 'LinkedIn' || $provider == 'Twitter' || $provider == 'Facebook' || $provider == 'Yahoo!' || $provider == 'MySpace'){
        $activity = array(
          'url'                    => 'http://www.janrain.com/',        /*required*//*string*/
          'action'                 => 'This is the action.',            /*required*//*string*/
          'user_generated_content' => 'This is user generated content.',/*string*/
          'title'                  => 'This is the title.',             /*string*/
          'description'            => 'This is the description',        /*string*/
          'action_links'           => array(                            /*multi-dimensional array or object*/
            array( 'text' => 'action link text.', 'href' => 'https://support.janrain.com/' )
        ),
          //Only one media entry allowed so only one can be uncommented at a time.
          'media'                  => array(                            /*multi-dimensional array or object*//*all three types are shown*/
            array( 'type' => 'image',/*up to five image arrays may be present, only the first is shown with the rest on a "See More"*/ 
                   'src' => 'http://docj27ko03fnu.cloudfront.net/rel/img/861d564d23ba416d9b480deac7c9f1f6.png', 
                 'href' => 'http://plugins.janrain.com/wordpress/'
            )
          ),

//          'media'                  => array(                            /*multi-dimensional array or object*/
//            array( 'type' => 'flash', 
//                   'swfsrc' => 'http://www.adobe.com/swf/software/flash/about/flash_animation.swf', 
//                   'imgsrc' => 'http://wwwimages.adobe.com/www.adobe.com/ubi/template/identity/adobe/screen/SiteHeader/logo.png',
//                   'width' => '90',/*width and height must be between 30 and 90 inclusive*/
//                   'height' => '90',
//                   'expanded_width' => '398',/*expanded width and height must be 398 or less*/
//                   'expanded_height' => '98'
//            )
//          ),

//          'media'                  => array(                            /*multi-dimensional array or object*/
//            array( 'type' => 'mp3',/*this is sometimes documented as "music", use "mp3"*/ 
//                   'src' => 'http://ontherecordpodcast.com/pr/otro/electronic/Get_Facebook_Friends_and_Twitter_Followers_While_You_Sleep.mp3', 
//                   'title' => 'Get Facebook Friends and Twitter Followers While You Sleep',
//                   'artist' => 'Tore Steen',
//                   'album' => 'On The Record Online'
//            )
//          ),
          'properties'           => array(                            /*multi-dimensional array or object*/
            'Potatoes' => 'mashed',
            'Apples'   => array( 'text' => 'property link', 'href' => 'http://www.apple.com/' )
          )
        );
        $activity = json_encode($activity);
        $post_data = array(
          'apiKey' => $rpx_api_key,
          'identifier' => $profile['identifier'],
          'activity' => $activity
        );
        $url = 'https://rpxnow.com/api/v2/activity';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $result = curl_exec($curl);
        if ($result == false){
          echo "\n".'URL:'.$url;
          echo "\n".'Curl error: ' . curl_error($curl);
          echo "\n".'HTTP code: ' . curl_errno($curl);
          echo "\n"; var_dump($post_data);
        }
        $activity_reply = json_decode($result);
        curl_close($curl);
        echo "\n activity:";
        echo "\n"; var_dump($activity);
        echo "\n activity_reply:";
        echo "\n"; var_dump($activity_reply);

      }

      /* Examples of using the accessToken to perform Facebook Graph API calls */
      /*Enagage Plus or higher subscription required for accessToken.*/
      if ($provider == 'Facebook'){//Graph is Facebook only. Some other providers also offer API access via the provided accessToken.

        //Make a "feed" post.
        $post_data = array(
          'access_token' => $auth_info['accessCredentials']['accessToken'],
          'message'  => 'MESSAGE',
          'picture' => 'http://www.janrain.com/favicon.png',
          'link' => 'http://www.janrain.com',
          'name' => 'NAME',
          'caption' => 'CAPTION',
          'description' => 'DESCRIPTION'
        );
        $url = 'https://graph.facebook.com/'.$auth_info['accessCredentials']['uid'].'/feed';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $result = curl_exec($curl);
        if ($result == false){
          echo "\n".'URL:'.$url;
          echo "\n".'Curl error: ' . curl_error($curl);
          echo "\n".'HTTP code: ' . curl_errno($curl);
          echo "\n"; var_dump($post_data);
        }
        $graph_feed = json_decode($result);
        curl_close($curl);
        echo "\nGRAPH feed post result:";
        echo "\n"; var_dump($graph_feed);

        //Pull the "me" profile
        $url = 'https://graph.facebook.com/me?access_token='.urlencode($auth_info['accessCredentials']['accessToken']);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $result = curl_exec($curl);
        if ($result == false){
          echo "\n".'URL:'.$url;
          echo "\n".'Curl error: ' . curl_error($curl);
          echo "\n".'HTTP code: ' . curl_errno($curl);
          echo "\n"; var_dump($post_data);
        }
        $graph_me = json_decode($result);
        curl_close($curl);
        echo "\nGRAPH 'me' profile:";
        echo "\n"; var_dump($graph_me);

        //Pull the "likes"
        $url = 'https://graph.facebook.com/'.$auth_info['accessCredentials']['uid'].'?access_token='.urlencode($auth_info['accessCredentials']['accessToken']);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $result = curl_exec($curl);
        if ($result == false){
          echo "\n".'URL:'.$url;
          echo "\n".'Curl error: ' . curl_error($curl);
          echo "\n".'HTTP code: ' . curl_errno($curl);
          echo "\n"; var_dump($post_data);
        }
        $graph_likes = json_decode($result);
        curl_close($curl);
        echo "\nGRAPH 'likes':";
        echo "\n"; var_dump($graph_likes);        
      }
    }


    /* STEP 4: Use the identifier as the unique key to sign the user into your system.
       This will depend on your website implementation, and you should add your own
       code here. The user profile is in $auth_info.
    */

    } else {
      // Gracefully handle auth_info error.  Hook this into your native error handling system.
      echo "\n".'An error occured: ' . $auth_info['err']['msg']."\n";
      var_dump($auth_info);
      echo "\n";
      var_dump($result);
    }
}else{
  // Gracefully handle the missing or malformed token.  Hook this into your native error handling system.
  echo 'Authentication canceled.';
}
$debug_out = ob_get_contents();
ob_end_clean();
?>
<html>
<head>
<title>Janrain Engage example</title>
</head>
<body>
<!-- content -->
<pre>
<?php echo $debug_out; ?>
</pre>
<!-- javascript -->
</body>
</html>
