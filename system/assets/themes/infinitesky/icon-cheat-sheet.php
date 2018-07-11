<?php
/**
 * Created by PhpStorm.
 * User: CMO
 * Date: 7/11/18
 * Time: 12:02 PM
 */
?>
<html>
<head>
    <title>ICON CHEAT SHEET</title>
    <style>
        html{
            background-color: #cccccc;
        }
        .icon-block{
            width: 20%;
            height: 10em;
            float: left;
            display: block;
        }
        img{
            width: 5em;
            height: auto;
        }
    </style>
</head>
<body>
<?php
if($dir = opendir(dirname(__FILE__).'/lib/images/icons/')){
    while (false !== ($entry = readdir($dir))) {
        if ($entry != "." && $entry != "..") {
            print '<div class="icon-block">
<img src="/system/assets/themes/infinitesky/lib/images/icons/'.$entry.'" /><br>'.$entry.'
</div>'."\n";
        }
    }
    closedir($dir);
}


if($dir = opendir(dirname(__FILE__).'/lib/images/icons/')){
    while (false !== ($entry = readdir($dir))) {
        if ($entry != "." && $entry != "..") {
            print '        &.icon_'.str_replace('.svg','',$entry).':before{background-image:url(../images/icons/'.$entry.');}'."\n";
        }
    }
    closedir($dir);
}
?>
</body>
</html>
