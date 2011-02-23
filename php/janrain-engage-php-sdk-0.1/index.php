<?php
/**
 * Copyright 2011
 * Janrain Inc.
 * All rights reserved.
 */
ob_start();
define('ENGAGE_LIB_DEVMODE', true);//define this as true to enable requirement checks
require_once('engage.lib.php');
require_once('index.inc.php');

$current_url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
$token_url = $current_url;

$action_map = array();
$action_map['auth_info'] = array(
	'token'		=> array('required'=>true, 'default'=>''),
	'api_key'	=> array('required'=>true, 'default'=>''),/* Fill in your API key as the default if you would like. */
	'format'	=> array('required'=>true, 'default'=>'json'),
	'extended'=> array('required'=>false, 'default'=>'')
);

$info_steps = array();
$info_steps['one'] = array (
	'title' => 'step_one_title',
	'info' => 'step_one_instructions',
	'trigger' => 'format'
);
$info_steps['two'] = array (
	'title' => 'step_two_title',
	'info' => 'step_two_instructions',
	'trigger' => 'token'
);
$info_steps['three'] = array (
	'title' => 'step_three_title',
	'info' => 'step_three_instructions',
	'trigger' => 'api_key'
);

$actions = array();
foreach ($action_map as $action=>$action_def) {
	$do = true;
	foreach ($action_def as $val_name=>$properties) {
		$val = $properties['default'];
		if (isset($_REQUEST[$val_name])) {
			$val = strip_tags($_REQUEST[$val_name]);
		}
		if (!isset($actions[$action])) {
			$actions[$action] = array();
		}
		if ($properties['required'] === true && empty($val)){
			$val = '';
			$do = false;
		}
		$actions[$action][$val_name] = $val;
	}
	$actions[$action]['do'] = $do;
}

$clean = true;
reset($actions);
while (list($action, $vals) = each($actions)) {
	if ($vals['do'] === true) {
		$actions[$action]['do'] = false;
		switch ($action) {
			case 'auth_info':
				$extended = false;
				if ($vals['extended'] == 'true') {
					$extended = true;
				}
				$result = engage_auth_info($vals['api_key'], $vals['token'], $vals['format'], $extended);
				if ($result === false) {
					$clean = false;
				}else{
					$actions['parse_result'] = array(
						'result' => $result,
						'format' => $vals['format'],
						'do' => true
					);
					if ($vals['format'] == 'json') {
						$actions['indent_json'] = array(
							'json' => $result,
							'do' => true
						);
					}
					if ($vals['format'] == 'xml') {
						$actions['indent_xml'] = array(
							'xml' => $result,
							'do' => true
						);
					}
				}
			break;
			case 'parse_result':
				$parse_result = engage_parse_result($vals['result'], $vals['format']);
				if ($parse_result === false) {
					$clean = false;
				} else {
					if (is_array($parse_result)) {
						if (isset($parse_result['err'])) {
							$engage_error = true;
						}
					} elseif (is_object($parse_result)) {
						if ($parse_result->err != '') {
							$engage_error = true;
						}
					}
					$actions['parse_dump'] = array(
						'data' => $parse_result,
						'do' => true
					);
				}
			break;
			case 'indent_json':
				$indent_json = indent_json($vals['json']);
				$actions['raw_dump'] = array(
					'data' => $indent_json,
					'do' => true
				);
			break;
			case 'indent_xml':
				$indent_xml = indent_xml($vals['xml']);
				$actions['raw_dump'] = array(
					'data' => $indent_xml,
					'do' => true
				);
			break;
			case 'raw_dump':
				$the_raw_result = $vals['data'];
			break;
			case 'parse_dump':
				$the_parse_result = print_r($vals['data'], true);
			break;
		}
	}
	if ($clean === false) {
		$the_errors = engage_get_errors();
		if ($the_errors === false) {
			$the_errors = array('unknown_error'=>'error');
		}
	}
}

$style = '';

if (!isset($the_raw_result)) {
	$the_raw_result = '';
}

if(!empty($the_raw_result)) {
	$style .= '		#raw_results_wrapper {
			display:block;
		}
';
}

if (!isset($the_parse_result)) {
	$the_result = '';
}

if(!empty($the_parse_result)) {
	$style .= '		#parse_results_wrapper {
			display:block;
		}
';
}

if (!isset($the_error)) {
	$the_error = '';
}

if (isset($the_errors)) {
	foreach ($the_errors as $error_msg=>$label) {
		$the_error .= "$error_msg" . "\n";
	}
}

if(!empty($the_error)) {
	$style .= '		#the_errors {
			display:block;
		}
';
}

$step_style = '';
foreach ($info_steps as $info_step=>$info_vals) {
	if ($actions['auth_info'][$info_vals['trigger']] != ''){
		$step_style = 
'		#'.$info_vals['info'].' {
			display:block !important;
		}
		#'.$info_vals['title'].' {
			color:#000 !important;
			background-color:#FFF !important;
		}
';
	}
}

if ($engage_error === true || !empty($the_error)){
	$step_style = 
'		#step_error_instructions {
			display:block !important;
		}
		#step_error_title {
			display:block !important;
			color:#000 !important;
			background-color:#FFF !important;
		}
';
}

$style .= $step_style;

ob_end_clean();
ob_start();
?>
<!DOCTYPE html>
<html>
	<head>
	<meta charset="UTF-8" />
	<title>Janrain Engage API console</title>
	<link rel="stylesheet" href="style.css" media="screen" />
	<style type="text/css">
<?php echo $style; ?>
	</style>
</head>
<body>
	<div id="toolkit_wrapper">
	<div id="main_toolkit">
		<div id="page_title">
			Janrain Engage API console
		</div>
		<div id="top_ui">
			<div id="api_menu">
				<div id="api_menu_title">Engage API</div>
				<ul id="api_menu_list">
					<li id="menu_auth_info">auth_info</ol>
					</li>
				</ul>
			</div>
			<div id="instructions_wrapper">
				<div id="instructions">
					<div id="step_one" class="instruction_step">
					<h3 id="step_one_title" class="instruction_title">Step One&nbsp;-&gt;</h3>
					<p id="step_one_instructions" class="instruction">
						<a class="rpxnow" onclick="return false;"
						href="https://tse01-janrain.rpxnow.com/openid/v2/signin?token_url=<?php echo urlencode($token_url); ?>"> Sign In </a><br />
						This will fill in the (one time use) token.
					</p>
					</div>
					<div id="step_two" class="instruction_step">
					<h3 id="step_two_title" class="instruction_title">Step Two&nbsp;-&gt;</h3>
					<p id="step_two_instructions" class="instruction">
						Copy your API key from your <a target="_blank" href="https://rpxnow.com/">Engage dashboard.</a><br />
						Paste the API key in to the apiKey field.<br />
						Select format and extended(Plus/Pro/Enterprise only) options.
						Click submit.
					</p>
					</div>
					<div id="step_three" class="instruction_step">
					<h3 id="step_three_title" class="instruction_title">Step Three&nbsp;&nbsp;</h3>
					<p id="step_three_instructions" class="instruction">
						You have completed the auth_info API call.<br />
						This is the point where your code would begin.<br />
						<a href="<?php echo htmlentities($current_url); ?>">Start over</a>
					</p>
					</div>
					<div id="step_error" class="instruction_step">
					<h3 id="step_error_title" class="instruction_title">Oops!&nbsp;&nbsp;&nbsp;</h3>
					<p id="step_error_instructions" class="instruction">
						Make sure your token URL domain is in your token URL domain list.<br />
						Remember that tokens are one-time use.<br />
						Reusing a token can result in "Data not found".<br />
						<a href="<?php echo htmlentities($current_url); ?>">Start over</a>.
					</p>
					</div>
				</div>
			</div>
		</div>
		<div id="main_form_wrapper">
			<form id="main_form" method="post">
				<label for="token">token</label>
				<input id="token" type="text" name="token" value="<?php echo htmlentities($actions['auth_info']['token']); ?>" />
				<label for="api_key">apiKey</label>
				<input id="api_key" type="text" name="api_key" value="<?php echo htmlentities($actions['auth_info']['api_key']); ?>" />
				<label for="format">format</label>
				<select id="format" name="format">
<?php
	$select_json = '';
	$select_xml = '';
	$selected = ' selected="selected"';
	if ($actions['auth_info']['format'] == 'json') {
		$select_json = $selected;
	} elseif ($actions['auth_info']['format'] == 'xml') {
		$select_xml = $selected;
	}
?>
					<option value="json"<?php echo $select_json; ?>>json</option>
					<option value="xml"<?php echo $select_xml; ?>>xml</option>
				</select>
				<label for="extended">extended</label>
				<input id="extended" type="checkbox" name="extended" value="true"<?php 
				if ($actions['auth_info']['extended'] == 'true') { echo ' checked="checked"'; } ?> />
				<input type="submit" name="go" value="submit" />
				<div id="api_sections">
					<div id="auth_info_section">
						<div id="auth_info_instructions">
						</div>
						<div id="auth_info_options">
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="output_wrapper">
			<div id="results_wrapper">
				<form id="results_form">
					<div id="raw_results_wrapper">
						<div id="raw_results_title">The response:</div>
						<textarea id="the_raw_results">
<?php echo htmlentities($the_raw_result); ?>

						</textarea>
					</div>
					<div id="parse_results_wrapper">
						<div id="parse_results_title">The parsed (as an array) response:</div>					
						<textarea id="the_parse_results">
<?php echo htmlentities($the_parse_result); ?>

						</textarea>
					</div>
				</form>
			</div>
			<div id="error_wrapper">
				<form id="error_form">
					<textarea id="the_errors">
<?php echo htmlentities($the_error); ?>

					</textarea>
				</form>
			</div>
		</div>
	</div>
	</div>
	<script type="text/javascript">
		var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");
		document.write(unescape("%3Cscript src='" + rpxJsHost +
			"rpxnow.com/js/lib/rpx.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		RPXNOW.overlay = true;
		RPXNOW.language_preference = 'en';
	</script>
</body>
</html><?php
ob_end_flush();
?>
