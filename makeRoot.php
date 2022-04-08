<!--Author: Zhi Create date:2021/7/4-->
<!--Run this when the root index file needs to be regenerated-->
<?php
include('function.php');
header("Content-type: text/html; charset=utf-8");

$lstDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/lstFiles';
$fileDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/files';
$keyDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/keyFiles';
$rootLstPath=$lstDir.DIRECTORY_SEPARATOR.'root.lst.cpabe' ;

if(!file_exists($lstDir)){createFolder($lstDir);}
if(!file_exists($fileDir)){createFolder($fileDir);}
if(!file_exists($keyDir)){createFolder($keyDir);}
//$host='http://10.20.22.150';
$host='http://127.0.0.1';

$strHeader="minister or master or member or uploadmanager\tuploadmanager\r\n";

createFile($rootLstPath,$strHeader);
//encryptFileByLinux($rootLstPath,'minister or master or member or uploadmanager');
echo 'root.lst.cpabe is ok.';


?>