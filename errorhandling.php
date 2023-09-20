<?php

function ucpErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch($errno)
    {
        case E_USER_ERROR:
        {
            die("FATAL ERROR: $errstr in $errfile on line $errline. ");
            break;
        }
        case E_USER_WARNING:
        {
            if($GLOBALS['access'] == DEVELOPER)
            {
                echo "WARNING: $errstr in $errfile on line $errline. ";
            }
            break;
        }
        case E_USER_NOTICE:
        {
            if($GLOBALS['access'] == DEVELOPER)
            {
                echo "NOTICE: $errstr in $errfile on line $errline. ";
            }
            break;
        }
        default:
            die('You should not see this');
    }
}

set_error_handler("ucpErrorHandler", E_USER_ERROR);
set_error_handler("ucpErrorHandler", E_USER_WARNING);
set_error_handler("ucpErrorHandler", E_USER_NOTICE);