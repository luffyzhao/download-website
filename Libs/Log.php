<?php
namespace Libs;

/**
*
*/
class Log
{

    public static function view($msg='')
    {
        echo $msg . '<br />';
        ob_flush();
        flush();
    }
}
