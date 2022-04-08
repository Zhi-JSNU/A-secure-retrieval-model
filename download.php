<?php
include('function.php');
header("Content-type: text/html; charset=utf-8");

	$filename=isset($_GET['filename'])?strval($_GET['filename']):'';

	if(empty($filename)){
		die('parameter missed.');
	}else{
		$fileDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/files';
		$filePath=$fileDir.DIRECTORY_SEPARATOR.$filename.'.cpabe';
		//echo $filePath;exit;
		$handle=@fopen($filePath,"r");
		if ($handle) {
			Header("Content-type: application/octet-stream");
			Header("Content-Disposition: attachment; filename=".$filename.'.file.cpabe');
			while (!feof ($handle)) {
				echo fread($handle,1024);
			}
		}else{
			echo 'The file "'.$filename.'" does not exist.<br />';
			echo '<a href="fm.php">click to return</a>';
		}
	}
?>