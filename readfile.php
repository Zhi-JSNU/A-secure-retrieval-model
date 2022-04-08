<!--Author: Zhi Create date:2021/7/4-->
<?php
include('function.php');
header("Content-type: text/html; charset=utf-8");
start_session(1440);

	$filename=isset($_GET['file'])?strval($_GET['file']):'';

	if(empty($filename)){
		die('parameter missed.');
	}else{
		$fileDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/files';
		$filePath=$fileDir.DIRECTORY_SEPARATOR.$filename.'.cpabe';
		//echo $filePath;exit;
		$attribute=isset($_SESSION["attribute"])?strval($_SESSION["attribute"]):'';
		if(empty($attribute)){
			echo 'Please relogin.';exit;
		}else{
			$content=readCpabeFile($filePath,$attribute);
			echo $content;
		}
	}



?>