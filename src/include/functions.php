<?php

function _log($message)
{
  if(LOG_ENABLED)
  {
    $log = "\r\n".date('Y-m-d H:i:s')."\t";
    if(is_array($message) || is_object($message))
    {
      $log .= print_r($message, true);
    }
    else
    {
      $log .= $message;
    }
    @error_log($log, 3, LOG_FILE);
  }
}
