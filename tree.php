<!--Author: Zhi Create date:2021/7/27-->
<?php
include('function.php');
start_session(1440);
header("Content-type: text/html; charset=utf-8");

$lstDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/lstFiles';
$fileDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/files';
$rootLstPath=$lstDir.DIRECTORY_SEPARATOR.'root.lst.cpabe' ;
$newFilePath=$lstDir.DIRECTORY_SEPARATOR.'newFile.cpabe' ;
$attribute=strval($_SESSION['attribute']);

$childrens=getChildrenByAttr($rootLstPath,$attribute);
var_dump($childrens);
exit;

if (!$fp = fopen($rootLstPath,'rb+')) {
	echo "Can`t open file:$rootLstPath.";
	exit;
}
$fileContent=readBigFile($rootLstPath);
echo $fileContent;exit;
$preg="/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i";
if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
	//var_dump($blockArr);exit;
	$blockNums=count($blockArr);//BLOCK的数量
}else{
	$blockNums=0;
}
//echo $blockNums;exit
if($blockNums>0){
	//var_dump($blockArr);
	$lenHeaderArr=count($blockArr);
	$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];
	//var_dump($lastHeaderLine);exit;
	$pos1=strrpos($fileContent,$lastHeaderLine);
	$pos2=$pos1+strlen($lastHeaderLine);
	fseek($fp,$pos2+2+913,SEEK_CUR);
	$content=fread($fp,1585);
	createFile($newFilePath,$content);
	echo 'OK.';
}else{
	echo 'error';
	exit;
}
exit;
?>