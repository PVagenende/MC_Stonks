<?

$pth = explode("/",$_SERVER['SCRIPT_FILENAME']);
$removed = array_pop($pth);
$path = "";

foreach ($pth as $section)
{
    if(strlen($section)>0)
    {
        $path = $path ."/".$section;
    }
}


include_once $path . '/etc/config.php';
include_once $path . '/etc/tools.php';
include_once $path . '/etc/debug.php';
include_once $path . '/class/MariaDB.php';