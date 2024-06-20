<!--Author: Zhi Create date:2021/7/4-->
<?php
include('function.php');
header("Content-type: text/html; charset=utf-8");
start_session(1440);

	$filename=isset($_GET['file'])?strval($_GET['file']):'';
    $dn=isset($_GET['dn'])?strval($_GET['dn']):'';
    $dir=isset($_GET['dir'])?strval($_GET['dir']):'';

    $lstDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/lstFiles';
    $lstCpabeFile=$lstDir.DIRECTORY_SEPARATOR.$dir.'.lst.cpabe' ;

	if(empty($filename)){
		die('parameter missed.');
	}else{
		$fileDir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'CPABE/files';
		$filePath=$fileDir.DIRECTORY_SEPARATOR.$filename.'.cpabe';
		//echo $filePath;exit;
		$attribute=isset($_SESSION["attribute"])?strval($_SESSION["attribute"]):'';
		if(empty($attribute)){
			echo 'Please relogin.';exit;
		}else if (empty($dir)){
		    //Liu Yixin
//			$content=readCpabeFile($filePath,$attribute);
            $content=readCpabeFileAndAtt($filePath,$attribute);
            echo $content;
		} else {
            $content=readChildCpabeFileAndAtt($filePath,$lstCpabeFile,$attribute,$dn,$dir);
            echo $content;
        }
	}



?>