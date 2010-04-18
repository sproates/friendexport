<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="A simple application to download your friend list.">
    <title>FriendExport</title>
    <link rel="stylesheet" type="text/css" href="assets/css/fb.css">
  </head>
  <body>
<?php

require_once('.'.DIRECTORY_SEPARATOR.'global.php');
require_once(INCLUDE_DIR.'facebook.php');

// Workarounds for new fb rules re. iframe apps

$_COOKIE = array();
$secret = FB_API_SECRET;
$facebook = new Facebook(FB_API_KEY, FB_API_SECRET);
$user = $facebook->get_loggedin_user();
if(!$user) {
  $user = $_REQUEST['uid'];
  if(!$user) {
    $facebook->redirect($facebook->get_login_url(FB_APP_URL, 1));
  }
  $key = $_REQUEST['key'];
  $token = $_REQUEST['token'];
  $check = md5($user.$secret);
  if($check != $token) {
    die("Invalid Signature");
  }
  $facebook->set_user($user, $key);
}
$key = $facebook->api_client->session_key;
$token = md5($user.$secret);

// This is used to append to internal links
$params = "uid=$user&key=$key&token=$token";

?>
    <div class="fbgreybox" style="width: 500px;">Welcome to FriendExport.</div>
    <br><br>
    <div class="fbbody">
      <p>This application lets you download your friend list. That is all.</p>
      <p>It doesn't do ads, invites or anything annoying.</p>
      <p>CSV is a type of spreadsheet. You can open it using Excel or a similar spreadsheet program.</p>
      <p>Please note that it is not possible to obtain a friend&#39;s email address this way, facebook doesn't allow it.</p>
    </div>
    <br><br>
    <div class="fbbody">
      <a href="<?php echo "export.php?format=CSV&$params"; ?>">Export your friend list as CSV</a>
    </div>
  </body>
</html>