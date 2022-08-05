<?php
debug('<!--Loading debug-->');
function debug($obj, $desc = NULL)
{
    if(DEBUG >= 1)
    {
        //echo "<hr>";
        if(isset($desc))
        {
            echo "<b>".$desc.":</b>";
        }
        if(is_array($obj))
        {
            echo "<pre>";
            print_r($obj);
            echo "</pre>";

        }
        else
        {
            echo $obj;
            echo "</br>";
        }
    }
}

if(DEBUG >= 2)
{
    debug($_SESSION,'SESSION');
}
if(DEBUG >= 3)
{
    debug($_POST,'POST');
}

if(isset($_GET['action']))
{
    if($_GET['action'] == 'destroy_session')
    {
        session_destroy();
    }
}
