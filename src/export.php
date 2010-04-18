<?php

error_reporting(E_NONE);
ini_set('display_errors', 0);

require_once('.'.DIRECTORY_SEPARATOR.'global.php');
_log('Entered export.php');
require_once(INCLUDE_DIR.'facebook.php');

/*

// No longer works for iframe apps.
// Leaving here for posterity.

$fb = new Facebook(FB_API_KEY, FB_API_SECRET);
$fb_user = $fb->require_login();

*/

// Workarounds for new fb rules re. iframe apps

$fb_user = $_REQUEST['uid'];
$key = $_REQUEST['key'];
$token = $_REQUEST['token'];
$secret = FB_API_SECRET;
$check = md5($fb_user.$secret);

if($check != $token) {
  die("Invalid Signature");
}
$fb = new Facebook(FB_API_KEY, FB_API_SECRET);
$fb->set_user($fb_user, $key);

$valid_formats = array('CSV');
if(!array_key_exists('format', $_REQUEST) || !in_array($_REQUEST['format'], $valid_formats))
{
  echo 'Invalid or no format specified<br><br>';
  exit;
}

if(is_numeric($fb_user))
{
  $info = $fb->api_client->users_getInfo($fb_user,'name');
  if(is_array($info))
  {
    _log("Download by: ".$info[0]['name']);
  }
  else
  {
    _log("User info not an array");
  }
}
else
{
  _log('Unable to get user info');
  _log($fb_user);
}

$friends = $fb->api_client->friends_get();
$all_info = $fb->api_client->users_getInfo($friends, array(
  'uid',
  'first_name',
  'last_name',
  'birthday',
  'relationship_status',
  'about_me',
  'interests',
  'meeting_for',
  'meeting_sex',
  'profile_url',
  'sex',
));
switch($_REQUEST['format'])
{
  case 'CSV':
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Expires: '.gmdate("D, d M Y H:i:s", time()).' GMT');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: maxage=1');
    header('Pragma: public');
    header('Content-type: text/comma-separated-values; charset=UTF-16LE');
    header('Content-Disposition: attachment; filename="FriendExport-'.date('Y-m-d').'.csv"');
    echo '"First Name","Last Name","Sex","Birthday","About",';
    echo '"Relationship Status","Looking For","With A","Interests","ID","Profile URL",'."\n";
    foreach($all_info AS $id => $friend)
    {
      echo csv_escape($friend['first_name']);
      echo csv_escape($friend['last_name']);
      echo csv_escape($friend['sex']);
      echo csv_escape($friend['birthday']);
      echo csv_escape($friend['about_me']);
      echo csv_escape($friend['relationship_status']);
      if(is_array($friend['meeting_for']))
      {
        $for = implode(' ', $friend['meeting_for']);
      }
      else
      {
        $for = '';
      }
      echo csv_escape($for);
      if(is_array($friend['meeting_sex']))
      {
        $sex = implode(' ', $friend['meeting_sex']);
      }
      else
      {
        $sex = '';
      }
      echo csv_escape($sex);
      if(is_array($friend['interests']))
      {
        $interests = implode(' ', $friend['interests']);
      }
      else
      {
        $interests = '';
      }
      echo csv_escape($interests);
      echo csv_escape($friend['uid']);
      echo csv_escape($friend['profile_url']);
      echo "\n";
      flush();
    }
    break;
  default:
    die('Invalid format');
    break;
}

function csv_escape($text)
{
  $text = mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
  $text = str_replace("\r", ' ', $text);
  $text = str_replace('"','""',$text);
  $text = '"' .$text . '",';
  return $text;
}

?>