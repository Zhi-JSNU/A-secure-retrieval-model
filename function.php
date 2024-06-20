<!--Author: Zhi Create date:2021/7/4-->
<?php
ini_set('display_errors', 'On');
//create a parent.lst.cpabe file
function makeParentLst($path,$teacherPath,$studentPath){
	if(file_exists($path)){
		unlink($path);
	}
	if (!$handle = fopen($path, 'ab')) {
		echo "The file $path does not opened.";
		exit;
	}
	//$strTemp="header xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n";	//32 'x'
	$strTemp="";
	$lenTeacher=strlen($teacherPath);
	$lenStudent=strlen($studentPath);
	$sectionTeacher='0-'.strval($lenTeacher);
	$sectionStudent=strval($lenTeacher).'-'.($lenTeacher+$lenStudent);

	$strTemp.="teacher $sectionTeacher\n";
	$strTemp.="student $sectionStudent\n";
	$strTemp.=$teacherPath.$studentPath."\n";


	if (fwrite($handle, $strTemp) === FALSE) {
		echo "The file $path does not writed.";
		exit;
	}
	echo "The file $path have been writed successfully.";
	fclose($handle);
}

//create multi-level directory
function createFolder($path){
   if (!file_exists($path))
   {
    createFolder(dirname($path));
    mkdir($path, 0777);
   }
}
//The plaintext string will be outputed to a temporary file,encrypted by Linux,and return the ciphertext.
function encodeStrByLinux($str,$attribute){
	$tmpFile='tmp';
	$tmpCpabe=$tmpFile.'.cpabe';
	//@@@@@@@@@@@@$tmpCpabe=$tmpFile;
	@chdir("/opt/lampp/htdocs/A-secure-retrieval-model-main/");
	$curDir=getcwd().DIRECTORY_SEPARATOR;
	$tmpFilePath=$curDir.$tmpFile;
	$tmpCpabePath=$curDir.$tmpCpabe;
	if(file_exists($tmpFilePath)){
		unlink($tmpFilePath);
	}
	if(file_exists($tmpCpabePath)){
		unlink($tmpCpabePath);
	}
	if (!$handle = fopen($tmpFilePath, 'ab')) {
		 echo "The file $tmpFile does not opened.";
		 exit;
	}
	if (fwrite($handle, $str) === FALSE) {
		echo "The file $tmpFile does not writed.";
		exit;
	}else{
		fclose($handle);

		//Liu Yixin begin
        chmod($tmpFilePath,0755);
        //Liu Yixin end

		$enOption="cpabe-enc pub_key $tmpFilePath '$attribute'";
		@system($enOption);
		$ciphertext=file_get_contents($tmpCpabePath);	//tmp.cpabe
		sleep(1);
		unlink($tmpCpabePath);
		return $ciphertext;
	}
}
//The ciphertext string will be outputed to a temporary file,decrypted by Linux,and return the plaintext.
/*function decodeStrByLinux($str,$key){
	$tmpFile='tmp';
	$tmpCpabe=$tmpFile.'.cpabe';
	@@@@@@@@@@@@$tmpCpabe=$tmpFile;
	@chdir("/var/www/CPABE/key");
	$curDir=getcwd().DIRECTORY_SEPARATOR;
	$tmpFilePath=$curDir.$tmpFile;
	$tmpCpabePath=$curDir.$tmpCpabe;
	if(file_exists($tmpFilePath)){
		unlink($tmpFilePath);
	}
	if(file_exists($tmpCpabePath)){
		unlink($tmpCpabePath);
	}
	if (!$handle = fopen($tmpCpabePath, 'ab')) {
		 echo "The file $tmpCpabe does not opened.";
		 exit;
	}
	if (fwrite($handle, $str) === FALSE) {
		echo "The file $tmpCpabe does not writed.";
		exit;
	}else{
		fclose($handle);
		chmod($tmpCpabePath,0777);
//		$deOption="cpabe-dec pub_key $key.key $tmpCpabePath";
        $deOption="cpabe-dec pub_key " . $key . ".key " . $tmpCpabePath;
		@system($deOption);
		$plaintext=file_get_contents($tmpFilePath);	//tmp
		unlink($tmpFilePath);
		return $plaintext;
	}
}*/

function decodeStrByLinux($str,$key){
    $tmpFile='tmp';
    $tmpCpabe=$tmpFile.'.cpabe';
    //@@@@@@@@@@@@$tmpCpabe=$tmpFile;
    @chdir("/opt/lampp/htdocs/A-secure-retrieval-model-main/");
    $curDir=getcwd().DIRECTORY_SEPARATOR;
    $tmpFilePath=$curDir.$tmpFile;
    $tmpCpabePath=$curDir.$tmpCpabe;
    if(file_exists($tmpFilePath)){
        unlink($tmpFilePath);
    }
    if(file_exists($tmpCpabePath)){
        unlink($tmpCpabePath);
    }
    file_put_contents($tmpCpabePath, '');
    if (!$handle = fopen($tmpCpabePath, 'ab')) {
        echo "The file $tmpCpabe does not opened.";
        exit;
    }
    if (fwrite($handle, $str) === FALSE) {
        echo "The file $tmpCpabe does not writed.";
        exit;
    }else{
        fclose($handle);
        chmod($tmpCpabePath,0777);
//		$deOption="cpabe-dec pub_key $key.key $tmpCpabePath";

        //Liu Yixin begin
        $deOption="cpabe-dec pub_key " . $key . ".key " . $tmpCpabePath;
        //Liu Yixin end

        @system($deOption);
        $plaintext=file_get_contents($tmpFilePath);	//tmp
        unlink($tmpFilePath);
        return $plaintext;
    }
}

//output a rand string
function generate_rand($length=8) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$rand ='';
	for ( $i = 0; $i < $length; $i++ )  {
		$rand .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	}
	return $rand;
}
function encryptFileByLinux($filePath,$attribute){
	chmod($filePath,0755);
	@chdir("/opt/lampp/htdocs/A-secure-retrieval-model-main/");

    //Liu Yixin begin
    $enOption = "cpabe-enc pub_key " . $filePath . " '" . $attribute . "'";
	//Liu Yixin end

    @system($enOption);
}
/*function decryptFileByLinux($cpabeFilePath,$key){
	chmod($cpabeFilePath,0777);
	@chdir("/opt/lampp/htdocs/A-secure-retrieval-model-main/");
	$deOption="cpabe-dec pub_key minister_key $cpabeFilePath";
	//$deOption="cpabe-dec pub_key $key.key $cpabeFilePath";
	//echo $key;//此处没有$key这个私有密钥，因此解密失败；
	@system($deOption);
}*/

function decryptFileByLinux($cpabeFilePath,$key){//$key此时只有一个属性，并没有or
    chmod($cpabeFilePath,0777);
    @chdir("/opt/lampp/htdocs/A-secure-retrieval-model-main/");
    //$deOption="cpabe-dec pub_key minister_key $cpabeFilePath";
    //$deOption="cpabe-dec pub_key $key.key $cpabeFilePath";
    //echo $key;//此处没有$key这个私有密钥，因此解密失败；

    //Liu Yixin begin
    if ("master" != $key){
        $deOption = "cpabe-dec pub_key " . $key . "_key " . $cpabeFilePath;
    } else {
        $deOption = "cpabe-dec pub_key " . $key . "1_key " . $cpabeFilePath;
    }
    //Liu Yixin end

    system($deOption);
//    var_dump($deOption);
}

function readParentLst($lstCpabeFile,$attribute){
	if(file_exists($lstCpabeFile)){
		if (!$handle = fopen($lstCpabeFile, 'rb')) {
			 return "The file $lstCpabeFile does not opened.";
			 exit;
		}
		//echo filesize($lstCpabeFile);exit;
		$slT=0;
		$slS=0;
		$slTS=0;
		if($attribute=='teacher' || $attribute=='student'){
			$tLine=fgets($handle,filesize($lstCpabeFile));
			$slT=strlen($tLine);
			//echo $tLine;
			$sLine=fgets($handle,filesize($lstCpabeFile));
			$slS=strlen($sLine);
			//echo $sLine;
			$slTS=$slT+$slS;
			//echo "$slT-$slS-$slTS";exit;
			if($attribute=='teacher'){
				$line=$tLine;
				$section=substr($line,8,-1);
				$arr=explode('-',$section);
				$lenArr=count($arr);
				$sa=intval($arr[0]);
				$sb=intval($arr[1]);
				$sl=$sb-$sa;
				//echo $sb+$slTS;
				$cipherText=file_get_contents($lstCpabeFile,0,null,$sa+$slTS,$sl);
			}
			if($attribute=='student'){
				$line=$sLine;
				$section=substr($line,8,-1);
				$arr=explode('-',$section);
				$lenArr=count($arr);
				$sa=intval($arr[0]);
				$sb=intval($arr[1]);
				$sl=$sb-$sa;
				$cipherText=file_get_contents($lstCpabeFile,0,null,$sa+$slTS);
			}
		}else{
			$cipherText=file_get_contents($lstCpabeFile,0,null,0);
		}

		//echo $cipherText;exit;
		$root=$_SERVER['DOCUMENT_ROOT'];
		$tmpFile='tmpCipher';
		$tmpFilePath="$root/CPABE/$tmpFile";
		if(file_exists($tmpFilePath)){
			unlink($tmpFilePath);
		}
		if (!$tHandle = fopen($tmpFilePath, 'ab')) {
			 return "The file $tmpFilePath does not opened.";
			 exit;
		}
		if (fwrite($tHandle, $cipherText) === FALSE) {
			return "The file $tmpFilePath does not writed.";
			exit;
		}else{
			fclose($tHandle);
			chmod($tmpFilePath,0755);
		}

		$decryedTxt=file_get_contents($tmpFilePath);
		//echo $decryedTxt;exit;
		$plaintext=decodeStrByLinux($decryedTxt,'uploadmanager.key');
		return $plaintext;
	}else{
		return "The file $lstCpabeFile does not exist.";
		exit;
	}
}

function readAttributeLst($lstFile,$lstCpabeFile,$key){
	if(file_exists($lstCpabeFile)){
		@$plaintext=decryptFileByLinux($lstCpabeFile,$key.'.key');
		if(file_exists($lstFile)){
			$lstContent=file_get_contents($lstFile);
			@encryptFileByLinux($lstFile,$key);
		}else{
			echo '<b>The file decrypted fail.</b></p>';
			exit;
		}
		return $lstContent;
	}else{
		return "The file $lstCpabeFile does not exist.";
		exit;
	}
}

function start_session($expire=3600){
    if ($expire == 0) {
        $expire = ini_get('session.gc_maxlifetime');
    } else {
        ini_set('session.gc_maxlifetime', $expire);
    }

    if (empty($_COOKIE['PHPSESSID'])) {
        session_set_cookie_params($expire);
        session_start();
    } else {
        session_start();
        setcookie('PHPSESSID', session_id(), time() + $expire);
    }
}

//echo indexInArray("bbbx",$configArr[0]);exit;
function indexInArray($str,$array){
	$len=count($array);
	for($i=0;$i<$len;$i++){
		if(in_array($str,$array[$i])){
			return strval($i);
		}
	}
	return 'none';
}
function findAttrByUsername($username,$array){
	$index=indexInArray($username,$array);
	if($index==='none'){
		return false;
	}else{
		$attribute=$array[$index]["attribute"];
		return $attribute;
	}
}
function findUserLevelByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	if($index==='none'){
		return false;
	}else{
		$level=$array[$index]["level"];
		return $level;
	}
}

//echo validateUserLogin("bbb","111111",$configArr[0]);exit;
function validateUserLogin($username,$password,$array){
	$index=indexInArray($username,$array);
	if($index==='none'){
		return 'error';	//The username or password is error.
	}else{
		$cfgPassword=md5($array[$index]["password"]);
		if($cfgPassword==md5($password)){
			return 'right';
		}else{
			return 'error';
		}
	}
}

function findGradeByLevel($level,$array){
	$index=indexInArray($level,$array);
	if($index==='none'){
		return false;
	}else{
		$grade=$array[$index]["grade"];
		return $grade;
	}
}

/*function getReadPermissionByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades='';
	if($index==='none'){
		return false;
	}else{
		for($i=0;$i<=$index;$i++){
			$grades.=$array[$i]["grade"].',';
		}
		$grades=substr($grades,0,-1);
		return $grades;
	}
}*/

//Liu Yixin
function getReadPermissionByAttr($lstPath, $attribute) {
    $grades = '';
    $file = fopen($lstPath, "r");
    if (!$file) {
        return "can't open the file!";
    }

    // 跳过第一行
    fgets($file);

    // 逐行读取，直到遇到空行
    while (($line = fgets($file)) !== false) {
        if (trim($line) === '') {
            break; // 空行，停止读取
        }
        // 移除数字、"-" 或制表符，并将行内容添加到 $grades
        $grades .= preg_replace('/[^a-zA-Z, ]/', '', $line) . ',';
    }
    fclose($file);

    // 移除末尾多余的逗号
    $grades = rtrim($grades, ',');

    // 将 $grades 字符串分割成数组
    $array = explode(',', $grades);

    $array = array_filter($array, function($val) {
        return trim($val) !== '';
    });

    // 检查 $attribute 是否已经存在于数组中
    if (!in_array($attribute, $array)) {
        // 如果 $attribute 不存在，则添加到数组末尾并重新构建 $grades 字符串
        $array[] = $attribute;
        $grades = implode(',', $array);
    } else {
        // 如果 $attribute 已存在，直接输出 $grades
        return $grades;
    }

    return $grades;
}
function getReadLstByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades='';
	$lenArr=count($array);
	if($index==='none'){
		return false;
	}else{
		for($i=$index;$i<$lenArr;$i++){
			$grades.=$array[$i]["grade"].',';
		}
		$grades=substr($grades,0,-1);
		return $grades;
	}
}
function getReadLstArrByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades=array();
	$lenArr=count($array);
	if($index==='none'){
		return false;
	}else{
		for($i=$index;$i<$lenArr;$i++){
			$grades[]=$array[$i]["grade"];
		}
		return $grades;
	}
}

function getParentsGradeByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades='';
	if($index==='none'){
		return false;
	}else{
		for($i=0;$i<$index;$i++){
			$grades.=$array[$i]["grade"].',';
		}
		$grades=substr($grades,0,-1);
		return $grades;
	}
}
function getChildrensGradeByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades='';
	$lenArr=count($array);
	if($index==='none'){
		return false;
	}else{
		for($i=$index;$i<($lenArr-1);$i++){
			$grades.=$array[$i+1]["grade"].',';
		}
		$grades=substr($grades,0,-1);
		return $grades;
	}
}
function getChildrensArrByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	$grades=array();
	$lenArr=count($array);
	if($index==='none'){
		return false;
	}else{
		for($i=$index;$i<($lenArr-1);$i++){
			$grades[]=$array[$i+1]["grade"];
		}
		return $grades;
	}
}
function getNameByAttr($attr,$array){
	$index=indexInArray($attr,$array);
	if($index==='none'){
		return false;
	}else{
		$name=$array[$index]["name"];
		return $name;
	}
}
function getUnselectedAttr($att,$array){
	$unAttr=array_diff($att,$array);
	return $unAttr;
}

function getUserRootDir($attr,$array){
	$arr=getChildrensArrByAttr($attr,$array);
	$lenArr=count($arr);
	$dir='';
	for($i=0;$i<$lenArr;$i++){
		$dirName=getNameByAttr($arr[$i],$array);
		$dir.='<li><span>&nbsp;</span>&nbsp;<a href="?action=show&step='.($i+1).'">'.$dirName.'</a></li>';
	}
	return $dir;
}
function getLstfileByStep($attr,$step,$array){
	$index=indexInArray($attr,$array);
	$lenArr=count($array);
	$next=$index+$step;
	$max=$lenArr-1;
	$lst='';

	if($step==0){
		$lst=$array[$index]["grade"];
	}else{
		if($next>=$lenArr){
			$lst=$array[$max]["grade"];
		}else{
			$lst=$array[$next]["grade"];
		}
	}
	return $lst;
}

function getLevelDiffByAttr($attr1,$attr2,$array){	//$attr2>$attr1
	$index1=indexInArray($attr1,$array);
	$index2=indexInArray($attr2,$array);
	$diff=$index2-$index1;
	return $diff;
}
/******************************************************************/

function readCpabeFile($cpabeFilePath,$attribute){
	if(file_exists($cpabeFilePath)){
		@$plaintext=decryptFileByLinux($cpabeFilePath,$attribute);
		//if($plaintext)
		//echo $plaintext;
		//else echo "11111";
		$filePath=substr($cpabeFilePath,0,-6);
		//echo $filePath;//去掉后缀.cpabe的文件名
		//echo $cpabeFilePath;
		if(file_exists($filePath)){
			//echo $filePath;
			$lstContent=file_get_contents($filePath);
			@encryptFileByLinux($filePath,$attribute);
			return $lstContent;
		}else{
			return '<b>The file decrypted fail.</b></p>';
		}
	}else{
		return "The file $cpabeFilePath does not exist.";
	}
}

function readCpabeFileSearch($cpabeFilePath,$attribute){
    if(file_exists($cpabeFilePath)){
        @$plaintext=decryptFileByLinux($cpabeFilePath,$attribute);
        $filePath=substr($cpabeFilePath,0,-6);
        //echo $filePath;//去掉后缀.cpabe的文件名
        //echo $cpabeFilePath;
        if(file_exists($filePath)){
            //echo $filePath;
            $lstContent=file_get_contents($filePath);
//            @encryptFileByLinux($filePath,$attribute);
            return $lstContent;
        }else{
            return '<b>The file decrypted fail.</b></p>';
        }
    }else{
        return "The file $cpabeFilePath does not exist.";
    }
}

function readLstHeader($cpabeLstPath,$line){
	if(file_exists($cpabeLstPath)){
		$header='';
		if (!$handle = fopen($cpabeLstPath, 'rb')) {
			echo "The file $cpabeLstPath does not opened.";
			exit;
		}
		for($i=0;$i<$line;$i++){
			$header.=fgets($handle, 1024);
		}
		fclose($handle);
		return $header;
	}else{
		return "The file $cpabeLstPath does not exist.";
		exit;
	}
}

function readLstCipher($cpabeLstPath,$headerLen){
	if(file_exists($cpabeLstPath)){
		$lstContent=file_get_contents($cpabeLstPath);
		$cipherContent=substr($lstContent,$headerLen);
		return $cipherContent;
	}else{
		return "The file $cpabeLstPath does not exist.";
		exit;
	}
}

function getSectionByAttribute($cpabeLstPath,$attribute){
	$handle = fopen($cpabeLstPath, 'rb');
	if ($handle) {
		fgets($handle, 1024);
		for($i=0;$i<3;$i++){
			$strLine='';
			$strLine=fgets($handle, 1024);
			$tmpArr=explode("\t",$strLine);
			$lenArr=count($tmpArr);
			if($lenArr==2){
				if(trim($tmpArr[1])==$attribute){
					return $tmpArr[0];
				}
			}
		}
		return false;
	}else{
		return false;
	}
	fclose($handle);
}

function getNextSectionByAttribute($cpabeLstPath,$attribute){
	$lines = file($cpabeLstPath);
	if ($lines) {
		for($i=1;$i<=3;$i++){
			$tmpArr=explode("\t",$lines[$i]);
			$lenArr=count($tmpArr);
			if($lenArr==2){
				if(trim($tmpArr[1])==$attribute && $i<3){
					$nextLine=$lines[($i+1)];
					$nArr=explode("\t",$nextLine);
					return $nArr[0];
				}
			}
		}
		return true;
	}else{
		return false;
	}
}
function getNextSectionsByAttribute($cpabeLstPath,$attribute){
	$lines = file($cpabeLstPath);
	$secArr=array();
	if ($lines) {
		for($i=1;$i<=3;$i++){
			$tmpArr=explode("\t",$lines[$i]);
			$lenArr=count($tmpArr);
			if($lenArr==2){
				if(trim($tmpArr[1])==$attribute){
					for($a=$i;$a<3;$a++){
						$nextLine=$lines[($a+1)];
						$nArr=explode("\t",$nextLine);
						$secArr[]=$nArr[0];
					}
				}
			}
		}
		if($secArr){
			return $secArr;
		}else{
			return true;
		}
	}else{
		return false;
	}
}

function adjustSectionByDiff($cpabeLstPath,$attribute,$diff){
	$lines = file($cpabeLstPath);
	$secArr=array();
	$strLine='';
	if ($lines) {
		$strLine.=$lines[0];
		for($i=1;$i<=3;$i++){
			$tmpArr=explode("\t",$lines[$i]);
			$lenArr=count($tmpArr);
			if($lenArr==2){
				if(trim($tmpArr[1])==$attribute){
					$nextLine=$lines[($i+1)];
					if(strlen($nextLine)>2){
						$thisArr=explode("-",$tmpArr[0]);
						$lines[$i]=$thisArr[0].'-'.(intval($thisArr[1])+$diff)."\t".$tmpArr[1];
						for($a=$i;$a<3;$a++){
							$cArr=explode("\t",$lines[$a]);
							$csArr=explode("-",$cArr[0]);
							$nArr=explode("\t",$lines[($a+1)]);
							$sArr=explode('-',$nArr[0]);
							$sa=intval($csArr[0])+intval($csArr[1]);
							$sb=$sArr[1];
							$lines[($a+1)]=$sa.'-'.$sb."\t$nArr[1]";
						}
					}else{
						$thisArr=explode("-",$tmpArr[0]);
						$lines[$i]=$thisArr[0].'-'.(intval($thisArr[1])+$diff)."\t".$tmpArr[1];
					}
				}
			}
			$strLine.=$lines[$i];
		}
		$strLine.=$lines[4];
		return $strLine;
	}else{
		return false;
	}
}
/******************************/
//^[a-zA-Z]+[a-z| |or]*(\t)[a-zA-Z0-9]+(\t)(\d)+(\n|\r)
//^[a-zA-Z]+[a-z| |or]*(\t)(\d)+(\n|\r)
function getBlockPlaintArray($fileContent){
	$blockPlain=array();
	$lines=count($fileContent);
	for($i=1;$i<$lines;$i++){
		if(preg_match("/^[a-zA-Z]+[a-z| |or]*(\t)(\d)+(\n|\r)/i",$fileContent[$i])){
			$blockPlain['line'][]=$i;
			$blockPlain['content'][]=$fileContent[$i];
		}
	}
	return $blockPlain;
}
//根据attribute获取其在LST中对应的所在行
function getAttributeLineByAtt($att,$blockArray){
	$lenBlock=count($blockArray['content']);
	for($i=0;$i<$lenBlock;$i++){
		$tmpArr=explode("\t",$blockArray['content'][$i]);
		if($att==$tmpArr[0]){
			return $blockArray['line'][$i];
		}
	}
	return false;
}
//根据attribute获取其在BLOCK数组中对应的index
function getAttributeIndexByAtt($att,$blockArray){
	$lenBlock=count($blockArray['content']);
	for($i=0;$i<$lenBlock;$i++){
		$tmpArr=explode("\t",$blockArray['content'][$i]);
		if($att==$tmpArr[0]){
			return $i;
		}
	}
	return false;
}
//^[a-zA-Z]+[a-z| |or]*(\t)[a-zA-Z0-9]+(\t)(\d)+(\n|\r)
//^[a-zA-Z]+[a-z| |or]*(\t)(\d)+(\n|\r)
function getHeaderArray($fileContent){
	$header=array();
	$lines=count($fileContent);
	for($i=1;$i<$lines;$i++){
		if(preg_match("/^[a-zA-Z]+[a-z| |or]*(\t)(\d)+(\n|\r)/i",$fileContent[$i])){
			$blockPlain['line'][]=$i;
			$blockPlain['content'][]=$fileContent[$i];
		}
	}
	return $blockPlain;
}

/***********************************************************************************************/
function createFile($path,$content){
	if(file_exists($path)){
		unlink($path);
	}
	if (!$handle = fopen($path, 'ab')) {
		return "The file $path does not opened.";
		exit;
	}
	if (fwrite($handle, $content) === FALSE) {
		return "The file $path does not writed.";
		exit;
	}
	//echo "The file $path have been writed successfully.";
	fclose($handle);
	return true;
}

//插入一段字符串
function str_insert($str, $i, $substr) {
	for($j=0; $j<$i; $j++){
		$startstr .= $str[$j];
	}
	for ($j=$i; $j<strlen($str); $j++){
		$laststr .= $str[$j];
	}
	$str = ($startstr . $substr . $laststr);
	return $str;
}

function readBigFile($filename,$length=NULL) {
	$content = '';
	//$handle = fopen($filename,'rb');
	if (file_exists($filename)) {
		if (!$handle = fopen($filename,'rb')) {
			echo "Can`t open file:$filename.";
			exit;
		}else{
			if(empty($length) || $length>8192){
				while (!feof($handle)) {
				  $content .= fread($handle, 8192);
				}
			}else{
				$content = fread($handle,$length);
			}
			fclose($handle);
		}
		return $content;
	} else {
		echo "The file $filename does not exist";
		exit;
	}
}

//从文件的某个位置开始读取字符，直到结束
function readFileFromOffset($filename,$offset,$length=NULL) {
	$content = '';
	$handle = fopen($filename,'rb');
	if (!$handle = fopen($filename,'rb')) {
		echo "Can`t open file:$filename.";
		exit;
	}else{
		fseek($handle,$offset,SEEK_CUR);
		if(empty($length) || $length>8192){
			while (!feof($handle)) {
			  $content .= fread($handle, 8192);
			}
		}else{
			$content = fread($handle,$length);
		}
		fclose($handle);
	}
	return $content;
}
//根据用户属性读取某个LST文件的子目录
function getChildrenByAttr($lstFilePath,$attribute){
	$fileContent=readBigFile($lstFilePath);
	if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
		$preg="/(\d+)(\-){1}(\d+)(\t{1})(?:.*$attribute.*)(\r\n)/i";
		if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
			//var_dump($blockArr);exit;
			$blockNums=count($blockArr);//BLOCK的数量
			/*获取密文区域开始*/
			//获取header最后一行的内容
			preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
			//var_dump($headArr);exit;
			$lenHeadArr=count($headArr[0]);
			$lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
			$offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
			$cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
			/*获取密文区域结束*/
			//echo $cipherContent;exit;
			//循环提取密文，获取下级子目录名称和路径
			$childrenArr=array();
			for($i=0;$i<$blockNums;$i++){
				$strLine=$blockArr[$i][0];
				$sectionA=$blockArr[$i][1];
				$sectionB=$blockArr[$i][3];
				$blockCipher=substr($cipherContent,$sectionA,$sectionB);
				//echo $blockCipher;exit;
				$blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
				if(preg_match_all("/(DC){1}(\t){1}(\S*)(\t){1}([0-9A-Za-z]{8}).lst.cpabe(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
					//var_dump($temArr);exit;
					foreach($temArr[3] as $k=>$v){
						$childrenArr['folder'][]=$v[0];
					}
					foreach($temArr[5] as $k=>$v){
						$childrenArr['cpabe'][]=$v[0];
					}
					//var_dump($childrenArr);exit;
				}
			}
//            var_dump($childrenArr);
			return $childrenArr;
		}else{
			$blockNums=0;
			return $blockNums;
		}
	}else{
		//return 'The file formats is error.';
		return 'err';
	}
}
//根据用户属性读取某个LST文件的文件列表
//function getFilesByAttr($lstFilePath,$attribute){
//	$fileContent=readBigFile($lstFilePath);
//	if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
//		$preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
//		if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
//			//var_dump($blockArr);exit;
//			$blockNums=count($blockArr);//BLOCK的数量
//			/*获取密文区域开始*/
//			//获取header最后一行的内容
//			preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
//			//var_dump($headArr);exit;
//			$lenHeadArr=count($headArr[0]);
//			$lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
//			$offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
//			$cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
//			/*获取密文区域结束*/
//			//echo $cipherContent;exit;
//			//循环提取密文，获取下级子目录名称和路径
//			$childrenArr=array();
//			$strFiles='';
//			for($i=0;$i<$blockNums;$i++){
//				$strLine=$blockArr[$i][0];
//				$sectionA=$blockArr[$i][1];
//				$sectionB=$blockArr[$i][3];
//				$blockCipher=substr($cipherContent,$sectionA,$sectionB);
//				//echo str_replace("\r\n",'@',$blockCipher);exit;
//				//var_dump($blockCipher);exit;
//				$blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
//				if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\|]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
//					//var_dump($temArr);exit;
//					$lenTemArr=count($temArr[0]);
//					if($lenTemArr){
//						for($s=0;$s<$lenTemArr;$s++){
//							$strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).'<!--a href="download.php?filename='.$temArr[5][$s][0].'"  target="_blank" title="down this file">'.$temArr[5][$s][0].'.file.cpabe</a-->'.str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$temArr[11][$s][0].str_repeat('&nbsp;',10).'<a href="readfile.php?file='.$temArr[5][$s][0].'" target="_read" title="show content">[read it]</a><br />';
//						}
//					}
//				}
//			}
//			return $strFiles;
//		}else{
//			$blockNums=0;
//			return $blockNums;
//		}
//	}else{
//		//return 'The file formats is error.';
//		return 'err';
//	}
//}

function getFilesByAttr($lstFilePath,$attribute,$dn,$dir){
    $fileContent=readBigFile($lstFilePath);
    if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
        $preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
        if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
//			var_dump($blockArr);
            $blockNums=count($blockArr);//BLOCK的数量
            /*获取密文区域开始*/
            //获取header最后一行的内容
            preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
            //var_dump($headArr);exit;
            $lenHeadArr=count($headArr[0]);
            $lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
            $offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
            $cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
            /*获取密文区域结束*/
            //echo $cipherContent;exit;
            //循环提取密文，获取下级子目录名称和路径
            $childrenArr=array();
            $strFiles='';
            for($i=0;$i<$blockNums;$i++){
                $strLine=$blockArr[$i][0];
                $sectionA=$blockArr[$i][1];
                $sectionB=$blockArr[$i][3];
                $blockCipher=substr($cipherContent,$sectionA,$sectionB);
                //echo str_replace("\r\n",'@',$blockCipher);exit;
                //var_dump($blockCipher);exit;
                $blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
                if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\|]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
                    //var_dump($temArr);exit;
                    $lenTemArr=count($temArr[0]);
                    if($lenTemArr){
                        for($s=0;$s<$lenTemArr;$s++){
//							$strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).'<!--a href="download.php?filename='.$temArr[5][$s][0].'"  target="_blank" title="down this file">'.$temArr[5][$s][0].'.file.cpabe</a-->'.str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$temArr[11][$s][0].str_repeat('&nbsp;',10).'<a href="readfile.php?file='.$temArr[5][$s][0].'" target="_read" title="show content">[read it]</a><br />';
                            //Liu Yixin
                            $strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$temArr[11][$s][0].str_repeat('&nbsp;',10).'<a href="readfile.php?file=' . $temArr[5][$s][0] . '&dn=' . $dn . '&dir=' . $dir . '" target="_read" title="show content">[read it]</a><br />';
                        }
                    }
                }
            }
            return $strFiles;
        }else{
            $blockNums=0;
            return $blockNums;
        }
    }else{
        //return 'The file formats is error.';
        return 'err';
    }
}

//根据用户属性和关键字读取某个LST文件的文件列表
//function getFilesByAttrAndKeyword($lstFilePath,$attribute,$keyword){
//	$fileContent=readBigFile($lstFilePath);
//	if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
//		$preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
//		if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
//			//var_dump($blockArr);exit;
//			$blockNums=count($blockArr);//BLOCK的数量
//			/*获取密文区域开始*/
//			//获取header最后一行的内容
//			preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
//			//var_dump($headArr);exit;
//			$lenHeadArr=count($headArr[0]);
//			$lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
//			$offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
//			$cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
//			/*获取密文区域结束*/
//			//echo $cipherContent;exit;
//			//循环提取密文，获取下级子目录名称和路径
//			$childrenArr=array();
//			$strFiles='';
//			$keywords='';
//			for($i=0;$i<$blockNums;$i++){
//				$strLine=$blockArr[$i][0];
//				$sectionA=$blockArr[$i][1];
//				$sectionB=$blockArr[$i][3];
//				$blockCipher=substr($cipherContent,$sectionA,$sectionB);
//				//echo str_replace("\r\n",'@',$blockCipher);exit;
//				//var_dump($blockCipher);exit;
//				//@@@@@@明文，不用解密$blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
//				$blockPlain=$blockCipher;//明文
//				if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\||\.|\/]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
//					//var_dump($temArr);exit;
//					$lenTemArr=count($temArr[0]);
//					if($lenTemArr){
//						for($s=0;$s<$lenTemArr;$s++){
//							//获取关键字URL
//							$keyUrl=$temArr[11][$s][0];
//							$keyFilePath=$_SERVER['DOCUMENT_ROOT'].$keyUrl;
//							//解密
//							$keywords=readCpabeFile($keyFilePath,$attribute);
//							//var_dump($keywords);exit;
//							$pos1=stripos($keywords,$keyword);
//							if ($pos1 === false){
//								continue;
//							}
//							$pattern="/($keyword)/i";
//							$replacement = '<font color="red"><b>${1}</b></font>';
//							$newKeywords=preg_replace($pattern, $replacement, $keywords);
//							$strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).'<!--a href="download.php?filename='.$temArr[5][$s][0].'"  target="_blank" title="down this file">'.$temArr[5][$s][0].'.file.cpabe</a-->'.str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$newKeywords.str_repeat('&nbsp;',10).'<a href="readfile.php?file='.$temArr[5][$s][0].'" target="_read" title="show content">[read it]</a><br />';
//						}
//					}
//				}
//			}
//			return $strFiles;
//		}else{
//			$blockNums=0;
//			return 'Not Found.';
//		}
//	}else{
//		//return 'The file formats is error.';
//		return 'err';
//	}
//}

function getFilesByAttrAndKeyword($lstFilePath,$attribute,$keyword){
    $fileContent=readBigFile($lstFilePath);

    //Liu Yixin begin
    $filePath=substr($lstFilePath,0,-10);
    $dir=basename($filePath);
    //Liu Yixin end

    if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
        $preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
        if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
            //var_dump($blockArr);exit;
            $blockNums=count($blockArr);//BLOCK的数量
            /*获取密文区域开始*/
            //获取header最后一行的内容
            preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
            //var_dump($headArr);exit;
            $lenHeadArr=count($headArr[0]);
            $lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
            $offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
            $cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
            /*获取密文区域结束*/
            //echo $cipherContent;exit;
            //循环提取密文，获取下级子目录名称和路径
            $childrenArr=array();
            $strFiles='';
            $keywords='';
            for($i=0;$i<$blockNums;$i++){
                $strLine=$blockArr[$i][0];
                $sectionA=$blockArr[$i][1];
                $sectionB=$blockArr[$i][3];
                $blockCipher=substr($cipherContent,$sectionA,$sectionB);
                //echo str_replace("\r\n",'@',$blockCipher);exit;
                //var_dump($blockCipher);exit;
                //@@@@@@明文，不用解密$blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
                $blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
//                $blockPlain=$blockCipher;//明文
//                var_dump($blockPlain);
                if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\||\.|\/]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
                    //var_dump($temArr);exit;
                    $lenTemArr=count($temArr[0]);
                    if($lenTemArr){
                        for($s=0;$s<$lenTemArr;$s++){
                            //获取关键字URL
                            $keyUrl=$temArr[11][$s][0];
                            $keyFilePath=$_SERVER['DOCUMENT_ROOT'].$keyUrl;
//                            var_dump($keyUrl);
                            //解密
                            $keywords=readCpabeFileSearch($keyFilePath,$attribute);
                            $filePath=substr($keyFilePath,0,-6);
                            //echo $filePath;//去掉后缀.cpabe的文件名
                            //echo $cpabeFilePath;
                            if(file_exists($filePath)){
                                //echo $filePath;
                                encryptFileByLinux($filePath,$temArr[7][$s][0]);
                            }
//                            $keywords=readCpabeFile($keyFilePath,$attribute);
//                            $keywords=readCpabeFileAndAtt($keyFilePath,$attribute);
//                            var_dump($keywords);
                            $pos1=stripos($keywords,$keyword);
                            if ($pos1 === false){
                                continue;
                            }
                            $pattern="/($keyword)/i";
                            $replacement = '<font color="red"><b>${1}</b></font>';
                            $newKeywords=preg_replace($pattern, $replacement, $keywords);
                            $strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).'<!--a href="download.php?filename='.$temArr[5][$s][0].'"  target="_blank" title="down this file">'.$temArr[5][$s][0].'.file.cpabe</a-->'.str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$newKeywords.str_repeat('&nbsp;',10).'<a href="readfile.php?file=' . $temArr[5][$s][0] .  '&dir=' . $dir . '" target="_read" title="show content">[read it]</a><br />';
                        }
                    }
                }
            }
            return $strFiles;
        }else{
            $blockNums=0;
            return 'Not Found.';
        }
    }else{
        //return 'The file formats is error.';
        return 'err';
    }
}

function X_getFilesByAttrAndKeyword($lstFilePath,$attribute,$keyword){
	$fileContent=readBigFile($lstFilePath);
	if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
		$preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
		if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
			//var_dump($blockArr);exit;
			$blockNums=count($blockArr);//BLOCK的数量
			/*获取密文区域开始*/
			//获取header最后一行的内容
			preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
			//var_dump($headArr);exit;
			$lenHeadArr=count($headArr[0]);
			$lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
			$offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
			$cipherContent=readFileFromOffset($lstFilePath,$offsetLastHeader+strlen($lastHeaderLine)+2);
			/*获取密文区域结束*/
			//echo $cipherContent;exit;
			//循环提取密文，获取下级子目录名称和路径
			$childrenArr=array();
			$strFiles='';
			$keywords='';
			for($i=0;$i<$blockNums;$i++){
				$strLine=$blockArr[$i][0];
				$sectionA=$blockArr[$i][1];
				$sectionB=$blockArr[$i][3];
				$blockCipher=substr($cipherContent,$sectionA,$sectionB);
				//echo str_replace("\r\n",'@',$blockCipher);exit;
				//var_dump($blockCipher);exit;
				$blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
				if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\|]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
					//var_dump($temArr);exit;
					$lenTemArr=count($temArr[0]);
					if($lenTemArr){
						for($s=0;$s<$lenTemArr;$s++){
							$keywords=$temArr[11][$s][0];
							$pos1=stripos($keywords,$keyword);
							if ($pos1 === false){
								continue;
							}
							$pattern="/($keyword)/i";
							$replacement = '<font color="red"><b>${1}</b></font>';
							$newKeywords=preg_replace($pattern, $replacement, $keywords);
							$strFiles.=$temArr[3][$s][0].str_repeat('&nbsp;',10).'<a href="download.php?filename='.$temArr[5][$s][0].'"  target="_blank" title="down this file">'.$temArr[5][$s][0].'.file.cpabe</a>'.str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$newKeywords.str_repeat('&nbsp;',10).'<a href="readfile.php?file='.$temArr[5][$s][0].'" target="_read" title="show content">[read it]</a><br />';
						}
					}
				}
			}
			return $strFiles;
		}else{
			$blockNums=0;
			return 'Not Found.';
		}
	}else{
		//return 'The file formats is error.';
		return 'err';
	}
}
//获取当前时间的毫秒数
function getMillisecond() {
	list($s1, $s2) = explode(' ', microtime());
	return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}

//根据用户属性递归读取所有匹配LST文件的目录数组
function getFoldersByAttr($lstFilePath,$attribute){
	$root=$_SERVER['DOCUMENT_ROOT'];
	$upDir='files';
	$lstDir='lstFiles';
	$arr=getChildrenByAttr($lstFilePath,$attribute);
	$result=array();
	if(!empty($arr) && $arr!='err'){
		$lenArr=count($arr);
		foreach($arr['cpabe'] as $k=>$v){
			$objLstFile="$root/CPABE/$lstDir/$v.lst";
			$objLstCpabeFile="$root/CPABE/$lstDir/$v.lst.cpabe";
			$x=getChildrenByAttr($objLstCpabeFile,$attribute);
			//echo $arr['folder'][$k].'@'.$v.'<br />';
			$result[]=$arr['folder'][$k];
			if(!empty($x) && $x!='err'){
				//var_dump($x['folder']);exit;
				$result[]=getFoldersByAttr($objLstCpabeFile,$attribute);
			}
		}
	}
	return $result;
}
//根据用户属性递归读取所有匹配LST文件的文件名数组
function getCpabesByAttr($lstFilePath,$attribute){
	$root=$_SERVER['DOCUMENT_ROOT'];
	$upDir='files';
	$lstDir='lstFiles';
	$arr=getChildrenByAttr($lstFilePath,$attribute);
	$result=array();
	if(!empty($arr) && $arr!='err'){
		$lenArr=count($arr);
		foreach($arr['cpabe'] as $k=>$v){
			$objLstFile="$root/CPABE/$lstDir/$v.lst";
			$objLstCpabeFile="$root/CPABE/$lstDir/$v.lst.cpabe";
			$x=getChildrenByAttr($objLstCpabeFile,$attribute);
			//echo $arr['folder'][$k].'@'.$v.'<br />';
			$result[]=$v;
			if(!empty($x) && $x!='err'){
				//var_dump($x['folder']);exit;
				$result[]=getCpabesByAttr($objLstCpabeFile,$attribute);
			}
		}
	}
	return $result;
}
//输出树形结构
function getArrayTree($arr,$num){
	$str='';
	$num++;
	foreach($arr as $k=>$v){
		if(is_array($v)){
			//$str[]=getArrayContent($v,$num);
			$str.=getArrayTree($v,$num);
		}else{
			//$str[]=$v;
			$str.=str_repeat('&nbsp;&nbsp;',$num).$v.'<br />';
		}
	}
	return $str;
}
//获取所有目录或路径名的字符串
function getArrayString($arr){
	$str='';
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$str.=getArrayString($v);
		}else{
			$str.=$v.',';
		}
	}
	return $str;
}
/*
function getArrayContent($arr){
	$str=array();
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$str[]=getArrayContent($v);
		}else{
			$str[]=$v;
		}
	}
	return $str;
}
*/

/*function readCpabeFileAndAtt($cpabeFilePath,$attribute){
//    var_dump($att);
    $root=$_SERVER['DOCUMENT_ROOT'];
    $lstDir='lstFiles';
    $dir="root";
    $lstCpabeFile="$root/CPABE/$lstDir/$dir.lst.cpabe";
    if(file_exists($cpabeFilePath)){
        decryptFileByLinux($cpabeFilePath,$attribute);
        $filePath=substr($cpabeFilePath,0,-6);
        //echo $filePath;//去掉后缀.cpabe的文件名
        //echo $cpabeFilePath;
        if(file_exists($filePath)){
            $fileName = basename($filePath);
//            var_dump($fileName);
            $att = readAtt($lstCpabeFile,$fileName,$attribute);
            //echo $filePath;
//            var_dump($att);

            $lstContent = file_get_contents($filePath);
            @encryptFileByLinux($filePath,$att);

            return $lstContent;
        }else{
//            $fileName = basename($filePath);
//            var_dump($fileName);
//            $att = readAtt($lstCpabeFile,$fileName,$attribute);
//            var_dump($att);
            return '<b>The file decrypted fail.</b></p>';
        }
    }else{
        return "The file $cpabeFilePath does not exist.";
    }
}*/

//Liu Yixin
function readCpabeFileAndAtt($cpabeFilePath,$attribute){
//    var_dump($att);
    $root=$_SERVER['DOCUMENT_ROOT'];
    $lstDir='lstFiles';
    $dir="root";
    $lstCpabeFile="$root/CPABE/$lstDir/$dir.lst.cpabe";
    if(file_exists($cpabeFilePath)){
        decryptFileByLinux($cpabeFilePath,$attribute);
        $filePath=substr($cpabeFilePath,0,-6);
        //echo $filePath;//去掉后缀.cpabe的文件名
        //echo $cpabeFilePath;
        if(file_exists($filePath)){
            $fileName = basename($filePath);
//            var_dump($fileName);
            $att = readAtt($lstCpabeFile,$fileName,$attribute,null,null);
            //echo $filePath;
//            var_dump($att);

            $lstContent = file_get_contents($filePath);
            @encryptFileByLinux($filePath,$att);

            return $lstContent;
        }else{
//            $fileName = basename($filePath);
//            var_dump($fileName);
//            $att = readAtt($lstCpabeFile,$fileName,$attribute);
//            var_dump($att);
            return '<b>The file decrypted fail.</b></p>';
        }
    }else{
        return "The file $cpabeFilePath does not exist.";
    }
}

//Liu Yixin
function readChildCpabeFileAndAtt($cpabeFilePath,$lstCpabeFile,$attribute,$dn,$dir){
    if(file_exists($cpabeFilePath)){
        decryptFileByLinux($cpabeFilePath,$attribute);
        $filePath=substr($cpabeFilePath,0,-6);
        //echo $filePath;//去掉后缀.cpabe的文件名
        //echo $cpabeFilePath;
        if(file_exists($filePath)){
            $fileName = basename($filePath);
//            var_dump($fileName);
            $att = readAtt($lstCpabeFile,$fileName,$attribute,$dn,$dir);
            //echo $filePath;
//            var_dump($att);

            $lstContent = file_get_contents($filePath);
            @encryptFileByLinux($filePath,$att);

            return $lstContent;
        }else{
//            $fileName = basename($filePath);
//            var_dump($fileName);
//            $att = readAtt($lstCpabeFile,$fileName,$attribute);
//            var_dump($att);
            return '<b>The file decrypted fail.</b></p>';
        }
    }else{
        return "The file $cpabeFilePath does not exist.";
    }
}

//Liu Yixin
function readAtt($lstCpabeFile,$fileName,$attribute,$dn,$dir){
    $att='';
    $allStr='';
    $fileContent=readBigFile($lstCpabeFile);
    if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
        $preg="/(\d+)(\-){1}(\d+)(\t)(?:.*$attribute.*)(\r\n)/i";
        if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
//			var_dump($blockArr);
            $blockNums=count($blockArr);//BLOCK的数量
            /*获取密文区域开始*/
            //获取header最后一行的内容
            preg_match_all("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$fileContent,$headArr,PREG_OFFSET_CAPTURE);
            //var_dump($headArr);exit;
            $lenHeadArr=count($headArr[0]);
            $lastHeaderLine=$headArr[0][($lenHeadArr-1)][0];
            $offsetLastHeader=intval($headArr[0][($lenHeadArr-1)][1]);
            $cipherContent=readFileFromOffset($lstCpabeFile,$offsetLastHeader+strlen($lastHeaderLine)+2);
            /*获取密文区域结束*/
            //echo $cipherContent;exit;
            //循环提取密文，获取下级子目录名称和路径
            $childrenArr=array();
            for($i=0;$i<$blockNums;$i++){
                $strLine=$blockArr[$i][0];
                $sectionA=$blockArr[$i][1];
                $sectionB=$blockArr[$i][3];
                $blockCipher=substr($cipherContent,$sectionA,$sectionB);
                //echo str_replace("\r\n",'@',$blockCipher);exit;
                //var_dump($blockCipher);exit;
                $blockPlain=decodeStrByLinux($blockCipher,'uploadmanager');//明文
//                file_put_contents('blockPlain.txt', $blockPlain);
                if(preg_match_all("/(F{1})(\t{1})([\S| ]*)(\t{1})([0-9A-Za-z]{8}).file.cpabe(\t{1})([\S| ]+)(\t{1})(\S+)(\t)([\S|\|]+)(\r\n)/i",$blockPlain,$temArr,PREG_OFFSET_CAPTURE)){
                    //var_dump($temArr);exit;
                    $lenTemArr=count($temArr[0]);
                    if($lenTemArr){
                        for($s=0;$s<$lenTemArr;$s++){
                            $allStr=$temArr[3][$s][0].str_repeat('&nbsp;',10).str_repeat('&nbsp;',10).$temArr[7][$s][0].str_repeat('&nbsp;',10).$temArr[9][$s][0].str_repeat('&nbsp;',10).$temArr[11][$s][0].str_repeat('&nbsp;',10).'<a href="readfile.php?file=' . $temArr[5][$s][0] . '&dn=' . $dn . '&dir=' . $dir . '" target="_read" title="show content">[read it]</a><br />';
                            if ($fileName == $temArr[5][$s][0]){
                                $att=$temArr[7][$s][0];
                                break;
                            }
                        }
//                        var_dump($all);
                    }
                }
            }

        }
    }
    return $att;
}

?>
