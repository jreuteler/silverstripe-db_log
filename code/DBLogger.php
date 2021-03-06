<?php

// log-category constants
define('SS_LOG_ERROR', 'ERROR');
define('SS_LOG_ACCESS', 'ACCESS');
define('SS_LOG_GENERAL', 'GENERAL');
define('SS_LOG_FILES', 'FILES');
define('SS_LOG_CONFIGURATION', 'CONFIGURATION');

class DBLogger
{

    public static function log($message, $method, $category)
    {
        $log = Log::create();

        if (is_array($message) || is_object($message)) {
            $log->Message = print_r($message, true);
        } else {
            $log->Message = $message;
        }
        $log->Method = $method;
        $log->Category = $category;
        $log->UserAgent = $_SERVER['HTTP_USER_AGENT'];

        // check if log happened during frontend or backend
        $source = 'FE'; // set to frontend as default
        if (is_subclass_of(Controller::curr(), "LeftAndMain")) {
            $source = 'BE'; // is backend
        }
        $log->Source = $source;
        $log->Action = Controller::curr()->getAction();

        // get Client IP
        $log->IpAddress = self::getClientIP();

        // Save member (if there is one)
        $member = Member::currentUser();
        if ($member) {
            $log->MemberID = $member->ID;
        }

        $log->write();

    }

    // Function to get the client IP address
    public static function getClientIP()
    {
        $ipAddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipAddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipAddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipAddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipAddress = getenv('REMOTE_ADDR');
        else
            $ipAddress = 'UNKNOWN';
        return $ipAddress;
    }

}