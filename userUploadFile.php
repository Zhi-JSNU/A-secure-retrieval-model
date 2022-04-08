<!--Author: Zhi Create date:2021/7/3-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Files</title>
<style type="text/css">
body,div,p {margin:0 auto; padding:0;}
.taCenter {text-align:center;line-height:25px;}
.alarm {border:1px #ccc dashed; width:500px; margin-top:30px; padding:20px;}
.alarm b {color:#f00;}
.callback {border:1px #ccc dashed; text-align:left; width:400px; line-height:25px; margin-top:30px; padding:20px;}
.callback b {font-weight:normal;color:#f00;}
.rtFM {width:500px; height:30px; overflow:hidden; margin-top:20px;}
.rtFM a {width:160px; height:28px; line-height:28px; text-align:center; display:inline-block; border:1px #ccc solid; background-color:#f8f8f8; text-decoration:none; float:right;}
.rtFM a:hover {background-color:#fff;}
</style>
<script type="text/javascript" src="./js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("select[name='dstFolder']").change(function(){
		if($(this).val()=='0'){
			$("input[name='folder']").show();
			$("div[name='inFolder']").show();
			$("input[name='folder']").focus();
		}else{
			$("input[name='folder']").hide();
			$("div[name='inFolder']").hide();
		}
	});
});
function keygen(){
	document.upFile.action='?action=KeyGen';
	document.upFile.submit();
}
</script>
</head>
<body>

<?php
set_time_limit(0);
include('config.inc.php');
include('function.php');
//session_start();
start_session(1440);

if (empty($_SESSION["name"])) {
	header("location:fm.php");
	exit;
}

$username=strval($_SESSION["name"]);
$attribute=strval($_SESSION['attribute']);
$action=isset($_GET['action'])?strval($_GET['action']):'';
$att=isset($_POST['att'])?trim($_POST['att']):'';
$keywords=isset($_POST['keywords'])?trim($_POST['keywords']):'';
$dstFolder=isset($_POST['dstFolder'])?trim($_POST['dstFolder']):'';

$folder=isset($_POST['folder'])?trim($_POST['folder']):'';

$checked='';
/*
if($att!='student' && $att!='teacher'){
	$att='teacher';
}
*/
//$readpermission=isset($_POST['readpermission'])?strval($_POST['readpermission']):'';
$host='http://10.20.22.150';

echo "welcome:".$username."(".$attribute.")";


$root=$_SERVER['DOCUMENT_ROOT'];
$upDir='files';
$lstDir='lstFiles';
$keyDir='keyFiles';
$rootLstFile="$root/CPABE/$lstDir/root.lst";
$rootLstCpabeFile="$root/CPABE/$lstDir/root.lst.cpabe";
$dir=isset($_GET["dir"])?strval($_GET["dir"]):'root';
$dn=isset($_GET["dn"])?strval($_GET["dn"]):'ROOT';

//$newLstPath="$root/CPABE/$lstDir/$newFloder.lst.cpabe";
$childrens=getChildrenByAttr($rootLstCpabeFile,$attribute);
//var_dump($childrens);exit;
$strOptions='';
if(!empty($childrens) && $childrens!='err'){
	if($childrens){
		$lenChildren=count($childrens['folder']);
		for($i=0;$i<$lenChildren;$i++){
			if($dstFolder==$childrens['cpabe'][$i]){
				$selected=" selected";
			}else{
				if(!empty($dn) && !empty($dir)){
					if($dir==$childrens['cpabe'][$i]){
						$selected=" selected";
					}else{
						$selected='';
					}
				}else{
					$selected='';
				}
			}
			$strOptions.='<option value="'.$childrens['cpabe'][$i].'"'.$selected.'>'.$childrens['folder'][$i].'</option>';
		}
	}
}

?>

<div class="taCenter">
	<h1>The File Name Encryption System</h1>
	<?php
	if(!empty($dn) && !empty($dir)){
		echo '<form method="post" name="upFile" action="?action=upload&dn='.$dn.'&dir='.$dir.'" enctype="multipart/form-data">';
	}else{
		echo '<form method="post" name="upFile" action="?action=upload" enctype="multipart/form-data">';
	}
	?>
		Input the file encryption attribute：<input type="text" name="att" value="<?=$att?>"> (e.g:minister or master or member)<br />
		<!--Please choose the folder that you will upload to:
		<select name="dstFolder">
			<option value="root"<?php	if($dstFolder=='root'){echo ' selected';} ?>>Root</option>
			<?//$strOptions?>
			<option value="0"<?php	if($dstFolder=='0'){echo ' selected';} ?>>New Folder</option>
		</select-->The folder that you will upload to：<input type="text" name="dirName" value="<?=$dn?>" readonly /><input type="hidden" name="dstFolder" value="<?=$dir?>" readonly /><br />
		<div name="inFolder"<?php if($dstFolder!='0'){echo ' style="display:none;"';} ?>>Input the new folder name:<input type="text" name="folder" /><br /></div>
		Choose Files：<input type="file" name="myfile"><br />
		Input the keyword about uploaded file：<input type="text" name="keywords" value="<?=$keywords?>">&nbsp;(keywords separated by space )<br />
		Click the "KeyGen" button to generate the encryption key：<input type="button" name="KeyGen" value="KeyGen" onClick='javascript:keygen();'><br /><input type="submit" value="start upload file">
	</form>
</div>
<?php
	if(!empty($dn) && !empty($dir)){
		echo '<p class="rtFM"><a href="fm.php?action=show&dn='.$dn.'&dir='.$dir.'">click to return</a></p>';
	}else{
		echo '<p class="rtFM"><a href="fm.php">click to return</a></p>';
	}
?>



<div class="taCenter">
<?php
if($action=='upload'){
	//var_dump($att);exit;
	if(empty($att)){
		echo '<p class="alarm"><b>Please input the attribute.</b></p>';exit;
	}
	if($dstFolder=='0' && empty($folder)){
		echo '<p class="alarm"><b>Please input the folder name.</b></p>';exit;
	}
	/*
	if(empty($readpermission)){
		echo '<p class="alarm"><b>Please input the readpermission.</b></p>';exit;
	}
	*/
	if(empty($keywords)){
		echo '<p class="alarm"><b>Please input the keywords.</b></p>';exit;
	}
	$root=$_SERVER['DOCUMENT_ROOT'];
	$upDir='files';
	$lstDir='lstFiles';
	$rootLstFile="$root/CPABE/$lstDir/root.lst";
	$rootLstCpabeFile="$root/CPABE/$lstDir/root.lst.cpabe";
	$dstLstCpabeFile="$root/CPABE/$lstDir/$dstFolder.lst.cpabe";
	$random=generate_rand(8);
	$newFloder=generate_rand(8);
	$newLstPath="$root/CPABE/$lstDir/$newFloder.lst.cpabe";


	$upErr=$_FILES["myfile"]["error"];
	if($upErr==4){
		echo '<p class="alarm"><b>Please choose the file to upload.</b></p>';
		exit;
	}
	if($upErr==0){
		echo '<p class="callback">';
		echo 'The attribute of encryption is:<b>'.$att.'</b>';
		echo "<br>";
		$readpermission=getReadPermissionByAttr($attribute,$configArr[1]);
		echo 'The listfile read permission is:<b>'.$readpermission.'</b>';
		echo '<br>';
		echo "The size of the file:<b>".$file_size=round($_FILES['myfile']['size']/1024,2).'KB</b>'.'<br>';
		if($file_size>5000){
			echo '<b>The file is too large to upload!</b></p>';
			exit();
		}
		echo "The type of the file:<b>".$file_type=$_FILES['myfile']['type']."</b><br>";
		//make sure that the file was uploaded by HTTP
		if(is_uploaded_file($_FILES['myfile']['tmp_name'])) {
			$uploaded_file=$_FILES['myfile']['tmp_name'];
			$move_to_file="$root/CPABE/$upDir/".$random;
			//move the file to the directory which you want
			if(move_uploaded_file($uploaded_file,$move_to_file)) {
				echo $_FILES['myfile']['name'].' <b>upload finished</b><br>';
			}else{
				echo $_FILES['myfile']['name'].' <b>upload failed</b><br>';
			}
		}else{
			echo $_FILES['myfile']['name'].' <b>upload failed</b><br>';
		}

		$realname=$_FILES['myfile']['name'];
		echo 'The name of the file is:<b>'.$_FILES['myfile']['name'].'</b><br>';
		echo "The random name of the file is:<b>".$random.'</b><br>';

		@encryptFileByLinux($move_to_file,$att);


		/**************处理部分开始**************************/
			if($dstFolder=='0'){//新建一个LST
				//echo $folder;
				$fileContent=readBigFile($rootLstCpabeFile);
				if (!$fp = fopen($rootLstCpabeFile,'rb+')) {
					echo "Can`t open file:$rootLstCpabeFile.";
					exit;
				}
				if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
					$preg="/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i";
					if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
						//var_dump($blockArr);exit;
						$blockNums=count($blockArr);//BLOCK的数量
					}else{
						$blockNums=0;
					}
				}else{
					echo 'The file formats is error.';
				}
				if($blockNums>0){
					if(preg_match_all("/(\d+)(\-){1}(\d+)(\t)(?:$att)(\r\n)/i",$fileContent,$outer)){
							//先正则找出匹配的header的属性部分
							if(preg_match("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$outer[0][0],$attrHeader)){
								$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
								if($attrHeader[5]==$blockArr[($lenHeaderArr-1)][5]){//如果匹配的header，是所有header中的最后一个，插入到DP下面
									//echo 'ssss';exit;
									//$realname='XX33.DOC';
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个匹配的header的内容
									//echo $lastHeaderLine;exit;
									$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
									$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($rootLstCpabeFile,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$afterHeader=readFileFromOffset($rootLstCpabeFile,$pos2);//匹配的header内容之后的所有内容。//+2，是因为多了一个空行
									//echo $afterHeader;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;

									/*start 在要写入的BLOCK内查找DC部分*/
									$dcPreg="/D[P|C]{1}(\t){1}(\S)*(\t){1}([0-9A-Za-z]){8}.lst.cpabe/i";
									if(preg_match_all($dcPreg,$plainBlock,$BlockDcArr,PREG_SET_ORDER)){
										$blockDcNums=count($BlockDcArr);//BLOCK的数量
									}else{
										$blockDcNums=0;
									}
									//var_dump($blockDcNums);exit;
									$strTmp='';
									$newPlainBlock='';
									if($blockDcNums>0){
										if(preg_match_all("/(DC){1}(\t){1}(\S)*(\t){1}([0-9A-Za-z]){8}.lst.cpabe(\r\n)/i",$plainBlock,$DCinBlockArr,PREG_OFFSET_CAPTURE)){
											$DCinBlockLen=count($DCinBlockArr[0]);
											$offsetLastDC=intval($DCinBlockArr[0][($DCinBlockLen-1)][1]);
											$lenLastDC=strlen($DCinBlockArr[0][($DCinBlockLen-1)][0]);
											$strTmp=substr($plainBlock,0,($offsetLastDC+$lenLastDC));
											$newPlainBlock.=$strTmp."DC\t$folder\t$newFloder.lst.cpabe\r\n".substr($plainBlock,($offsetLastDC+$lenLastDC));
											//echo $newPlainBlock;exit;
										}else{
											echo 'parten err1.';exit;
										}
									}else{
										if(preg_match("/(DP){1}(\t){1}(\S)*(\r\n)/i",$plainBlock,$BlockDpArr,PREG_OFFSET_CAPTURE)){
											$strTmp=$BlockDpArr[0][0];
											$lenBlockDP=strlen($BlockDpArr[0][0]);
											$newPlainBlock.=$strTmp."DC\t$folder\t$newFloder.lst.cpabe\r\n".substr($plainBlock,$lenBlockDP);
											//echo $newPlainBlock;exit;
										}else{
											echo 'parten err2.';exit;
										}
									}
									/*end*/

									//echo $newPlainBlock;exit;
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($newPlainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$newPlainBlock;
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$newHeaderLine="$sectionA-$cipherLen\t$att\r\n";
									//echo $newHeaderLine;exit;
									$newContent=$beforeHeader.$newHeaderLine.$oldCipher.$ciphertext;
									//echo $newContent;exit;
									fclose($fp);
									createFile($rootLstCpabeFile,$newContent);
								}else{//插入到DP下面
									//echo 'bbdbfbd';exit;
									//$realname='XX44.DOC';
									//var_dump($attrHeader);exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									//获取匹配的header在所有HEADER中的index
									$index=0;
									for($i=0;$i<$lenHeaderArr;$i++){
										if($attrHeader[5]==$blockArr[$i][5]){
											$index=$i;
										}
									}
									//echo $index;exit;
									$indexHeaderLine=$blockArr[$index][0];//匹配的header的内容
									//echo $indexHeaderLine;exit;
									$pos1=strrpos($fileContent,$indexHeaderLine);//匹配的header的内容在LST文件中最后一次出现的位置。
									//echo $pos1;exit;
									$beforeHeader=readFileFromOffset($rootLstCpabeFile,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个header的内容
									//echo $lastHeaderLine;exit;
									$pos2=strrpos($fileContent,$lastHeaderLine);//最后一个header的，后面内容的起始位置。
									$pos3=$pos2+strlen($lastHeaderLine);//最后一个header的，后面内容的起始位置。
									//echo $pos3;exit;
									$afterHeader=readFileFromOffset($rootLstCpabeFile,$pos3);//匹配的header内容之后的所有内容。包含一个空行。
									//echo $afterHeader;exit;
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									$lenCipherBlock1=strlen($cipherBlock);//更新前匹配header的相应密文的长度
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;
									/*start 在要写入的BLOCK内查找DC部分*/
									$dcPreg="/D[P|C]{1}(\t){1}(\S)*(\t){1}([0-9A-Za-z]){8}.lst.cpabe/i";
									if(preg_match_all($dcPreg,$plainBlock,$BlockDcArr,PREG_SET_ORDER)){
										$blockDcNums=count($BlockDcArr);//BLOCK的数量
									}else{
										$blockDcNums=0;
									}
									//var_dump($blockDcNums);exit;
									$strTmp='';
									$newPlainBlock='';
									if($blockDcNums>0){
										if(preg_match_all("/(DC){1}(\t){1}(\S)*(\t){1}([0-9A-Za-z]){8}.lst.cpabe(\r\n)/i",$plainBlock,$DCinBlockArr,PREG_OFFSET_CAPTURE)){
											$DCinBlockLen=count($DCinBlockArr[0]);
											$offsetLastDC=intval($DCinBlockArr[0][($DCinBlockLen-1)][1]);
											$lenLastDC=strlen($DCinBlockArr[0][($DCinBlockLen-1)][0]);
											$strTmp=substr($plainBlock,0,($offsetLastDC+$lenLastDC));
											$newPlainBlock.=$strTmp."DC\t$folder\t$newFloder.lst.cpabe\r\n".substr($plainBlock,($offsetLastDC+$lenLastDC));
										}else{
											echo 'parten err1.';exit;
										}
									}else{
										if(preg_match("/^(DP){1}(\t){1}(\S)*(\r\n)/i",$plainBlock,$BlockDpArr,PREG_OFFSET_CAPTURE)){
											$strTmp=$BlockDpArr[0][0];
											$lenBlockDP=strlen($BlockDpArr[0][0]);
											$newPlainBlock.=$strTmp."DC\t$folder\t$newFloder.lst.cpabe\r\n".substr($plainBlock,$lenBlockDP);
										}else{
											echo 'parten err2.';exit;
										}
									}
									/*end*/
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($newPlainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$newPlainBlock;//新的BLOCK的密文内容
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$diff=$cipherLen-$lenCipherBlock1;//更新密文后，与之前密文长度的差值，可能为正也可能为负
									//echo $diff;exit;
									//循环更新header区间和相应密文
									$nHeader='';
									$nCipher='';
									for($i=0;$i<$lenHeaderArr;$i++){
										$a='sa'.$i;
										$b='sb'.$i;
										$c='cipher'.$i;
										$t='attr'.$i;
										${$a}=$blockArr[$i][1];
										${$b}=$blockArr[$i][3];
										${$c}=substr($afterHeader,${$a}+2,${$b});
										//echo ${$c};exit;
										//echo $index;exit;
										if($i==$index){
											${$b}=$blockArr[$i][3]+$diff;
											${$c}=$ciphertext;
										}
										if($i>$index){
											${$a}=$blockArr[$i][1]+$diff;
										}
										if($i<$index){
											${$t}='';
										}else{
											${$t}=${$a}.'-'.${$b}."\t".$blockArr[$i][5]."\r\n";
										}
										$nHeader.=${$t};
										$nCipher.=${$c};
									}
									//echo $nHeader;exit;
									//echo $nCipher;exit;
									$newContent=$beforeHeader.$nHeader."\r\n".$nCipher;
									//echo $newContent;exit;
									fclose($fp);
									createFile($rootLstCpabeFile,$newContent);
								}
							}
					}else{//没有找到，先更新HEADER部分，然后再在文件尾部追加BLOCK
							//echo 'bb';exit;
							//先找到匹配BLOCK的Header部分的最后一个位置
								$lenHeaderArr=count($blockArr);//插入新header之前，匹配的header数量
								//var_dump($blockArr);exit;
								$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//插入新header之前，最后一个匹配的header的内容
								//echo $lastHeaderLine;exit;
								$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
								$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($rootLstCpabeFile,0,$pos2);//匹配的header内容之前的所有内容
								//echo $beforeHeader;exit;
								$afterHeader=readFileFromOffset($rootLstCpabeFile,$pos2);//匹配的header内容之后的所有内容。包含一个空行
								//echo $afterHeader;exit;
								$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
								//echo $lenCipherBlock;exit;

								//新BLOCK内容明文部分
								$plaintext='';
								//$realname='XX22.DOC';
								$plaintext.="DP\t\t\r\n";
								$plaintext.="DC\t$folder\t$newFloder.lst.cpabe\r\n";
								//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
								$ciphertext=$plaintext;
								$cipherLen=strlen($ciphertext);
								$newHeaderLine="$lenCipherBlock-$cipherLen\t$att\r\n";
								//echo $cipherLen;exit;
								$newContent=$beforeHeader.$newHeaderLine.$afterHeader.$ciphertext;
								//echo $newContent;exit;
								fclose($fp);
								createFile($rootLstCpabeFile,$newContent);
					}
				}else{//LST内容为空的时候，直接在文件尾部开始插入BLOCK
					$plaintext='';
					$plaintext.="DP\t\t\r\n";
					$plaintext.="DC\t$folder\t$newFloder.lst.cpabe\r\n";
					//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
					$ciphertext=$plaintext;
					$cipherLen=strlen($ciphertext);
					$blockPlain="0-$cipherLen\t$att\r\n";
					fseek($fp,0,SEEK_END);
					fwrite($fp,$blockPlain."\r\n");
					fwrite($fp,$ciphertext);
				}


			
			$strHeader="minister or master or member or uploadmanager\tuploadmanager\r\n";
			createFile($newLstPath,$strHeader);
			//echo $newFloder.'.lst.cpabe is ok.';
			/*********************写入新LST文件*********开始*****************/
			
				$fileContent=readBigFile($newLstPath);
				if (!$fp = fopen($newLstPath,'rb+')) {
					echo "Can`t open file:$newLstPath.";
					exit;
				}
				if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
					//var_dump($matches);exit;
					$preg="/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i";
					if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
						//var_dump($blockArr);exit;
						$blockNums=count($blockArr);//BLOCK的数量
					}else{
						$blockNums=0;
					}
					if($blockNums>0){
						//var_dump($blockArr);exit;
						//判断有无与属性相对应的HEADER部分
						//不完全匹配/(\d+)(\-){1}(\d+)(\t)(?:.*$att.*)(\r\n)/i
						if(preg_match_all("/(\d+)(\-){1}(\d+)(\t)(?:$att)(\r\n)/i",$fileContent,$outer)){
							//var_dump($blockArr);exit;
							//var_dump($outer);
							//echo 'aa';exit;
							//先正则找出匹配的header的属性部分
							if(preg_match("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$outer[0][0],$attrHeader)){
								$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
								if($attrHeader[5]==$blockArr[($lenHeaderArr-1)][5]){//如果匹配的header，是所有header中的最后一个，追加
									//$realname='XX33.DOC';
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个匹配的header的内容
									//echo $lastHeaderLine;exit;
									$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
									$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($newLstPath,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$afterHeader=readFileFromOffset($newLstPath,$pos2);//匹配的header内容之后的所有内容。//+2，是因为多了一个空行
									//echo $afterHeader;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;
									$keyArr=explode(' ',$keywords);
									$lenKeyword=count($keyArr);
									$strKeyword='';
									for($k=0;$k<$lenKeyword;$k++){
										if($keyArr[$k]){
											$strKeyword.=$keyArr[$k];
											if($k<($lenKeyword-1)){
												$strKeyword.='|';
											}
										}
									}
									$rndFile=generate_rand(8).'.keyword';
									$keyFilePath="$root/CPABE/$keyDir/$rndFile";
									$keyFileUrl="/CPABE/$keyDir/$rndFile";
									//把关键字集合保存到随机文件里
									createFile($keyFilePath,$strKeyword);
									//加密关键字文件
									encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
									$plainBlock.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
									//echo $plainBlock;exit;
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$plainBlock;//新的BLOCK的密文内容
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$newHeaderLine="$sectionA-$cipherLen\t$att\r\n";
									//echo $newHeaderLine;exit;
									$newContent=$beforeHeader.$newHeaderLine.$oldCipher.$ciphertext;
									//echo $newContent;exit;
									fclose($fp);
									createFile($newLstPath,$newContent);
								}else{//插入
									//$realname='XX44.DOC';
									//var_dump($attrHeader);exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									//获取匹配的header在所有HEADER中的index
									$index=0;
									for($i=0;$i<$lenHeaderArr;$i++){
										if($attrHeader[5]==$blockArr[$i][5]){
											$index=$i;
										}
									}
									//echo $index;exit;
									$indexHeaderLine=$blockArr[$index][0];//匹配的header的内容
									//echo $indexHeaderLine;exit;
									$pos1=strrpos($fileContent,$indexHeaderLine);//匹配的header的内容在LST文件中最后一次出现的位置。
									//echo $pos1;exit;
									$beforeHeader=readFileFromOffset($newLstPath,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个header的内容
									//echo $lastHeaderLine;exit;
									$pos2=strrpos($fileContent,$lastHeaderLine);//最后一个header的，后面内容的起始位置。
									$pos3=$pos2+strlen($lastHeaderLine);//最后一个header的，后面内容的起始位置。
									//echo $pos3;exit;
									$afterHeader=readFileFromOffset($newLstPath,$pos3);//匹配的header内容之后的所有内容。包含一个空行。
									//echo $afterHeader;exit;
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									$lenCipherBlock1=strlen($cipherBlock);//更新前匹配header的相应密文的长度
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;
									$keyArr=explode(' ',$keywords);
									$lenKeyword=count($keyArr);
									$strKeyword='';
									for($k=0;$k<$lenKeyword;$k++){
										if($keyArr[$k]){
											$strKeyword.=$keyArr[$k];
											if($k<($lenKeyword-1)){
												$strKeyword.='|';
											}
										}
									}
									$rndFile=generate_rand(8).'.keyword';
									$keyFilePath="$root/CPABE/$keyDir/$rndFile";
									$keyFileUrl="/CPABE/$keyDir/$rndFile";
									//把关键字集合保存到随机文件里
									createFile($keyFilePath,$strKeyword);
									//加密关键字文件
									encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
									$plainBlock.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
									//echo $plainBlock;exit;
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$plainBlock;//新的BLOCK的密文内容
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$diff=$cipherLen-$lenCipherBlock1;//更新密文后，与之前密文长度的差值，可能为正也可能为负
									//echo $diff;exit;
									//循环更新header区间和相应密文
									$nHeader='';
									$nCipher='';
									for($i=0;$i<$lenHeaderArr;$i++){
										$a='sa'.$i;
										$b='sb'.$i;
										$c='cipher'.$i;
										$t='attr'.$i;
										${$a}=$blockArr[$i][1];
										${$b}=$blockArr[$i][3];
										${$c}=substr($afterHeader,${$a}+2,${$b});
										//echo ${$c};exit;
										//echo $index;exit;
										if($i==$index){
											${$b}=$blockArr[$i][3]+$diff;
											${$c}=$ciphertext;
										}
										if($i>$index){
											${$a}=$blockArr[$i][1]+$diff;
										}
										if($i<$index){
											${$t}='';
										}else{
											${$t}=${$a}.'-'.${$b}."\t".$blockArr[$i][5]."\r\n";
										}
										$nHeader.=${$t};
										$nCipher.=${$c};
									}
									//echo $nHeader;exit;
									//echo $nCipher;exit;
									$newContent=$beforeHeader.$nHeader."\r\n".$nCipher;
									//echo $newContent;exit;
									fclose($fp);
									createFile($newLstPath,$newContent);
								}
							}
						}else{//没有找到，先更新HEADER部分，然后再在文件尾部追加BLOCK
							//echo 'bb';exit;
							//先找到匹配BLOCK的Header部分的最后一个位置
								$lenHeaderArr=count($blockArr);//插入新header之前，匹配的header数量
								//var_dump($blockArr);exit;
								$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//插入新header之前，最后一个匹配的header的内容
								//echo $lastHeaderLine;exit;
								$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
								$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($newLstPath,0,$pos2);//匹配的header内容之前的所有内容
								//echo $beforeHeader;exit;
								$afterHeader=readFileFromOffset($newLstPath,$pos2);//匹配的header内容之后的所有内容。包含一个空行
								//echo $afterHeader;exit;
								$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
								//echo $lenCipherBlock;exit;

								//新BLOCK内容明文部分
								$plaintext='';
								//$realname='XX22.DOC';
								$plaintext.="DP\troot.lst.cpabe\r\n";
								//$plaintext.="DC\t\t\r\n";
								$keyArr=explode(' ',$keywords);
								$lenKeyword=count($keyArr);
								$strKeyword='';
								for($k=0;$k<$lenKeyword;$k++){
									if($keyArr[$k]){
										$strKeyword.=$keyArr[$k];
										if($k<($lenKeyword-1)){
											$strKeyword.='|';
										}
									}
								}
								$rndFile=generate_rand(8).'.keyword';
								$keyFilePath="$root/CPABE/$keyDir/$rndFile";
								$keyFileUrl="/CPABE/$keyDir/$rndFile";
								//把关键字集合保存到随机文件里
								createFile($keyFilePath,$strKeyword);
								//加密关键字文件
								encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
								$plaintext.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
								//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
								$ciphertext=$plaintext;
								$cipherLen=strlen($ciphertext);
								$newHeaderLine="$lenCipherBlock-$cipherLen\t$att\r\n";
								//echo $cipherLen;exit;
								$newContent=$beforeHeader.$newHeaderLine.$afterHeader.$ciphertext;
								//echo $newContent;exit;
								fclose($fp);
								createFile($newLstPath,$newContent);
						}
					}else{//LST内容为空的时候，直接在文件尾部开始插入BLOCK
						$plaintext='';
						//$realname='XXX.DOC';
						$plaintext.="DP\troot.lst.cpabe\r\n";
						//$plaintext.="DC\t\t\r\n";
						$keyArr=explode(' ',$keywords);
						$lenKeyword=count($keyArr);
						$strKeyword='';
						for($k=0;$k<$lenKeyword;$k++){
							if($keyArr[$k]){
								$strKeyword.=$keyArr[$k];
								if($k<($lenKeyword-1)){
									$strKeyword.='|';
								}
							}
						}
						$rndFile=generate_rand(8).'.keyword';
						$keyFilePath="$root/CPABE/$keyDir/$rndFile";
						$keyFileUrl="/CPABE/$keyDir/$rndFile";
						//把关键字集合保存到随机文件里
						createFile($keyFilePath,$strKeyword);
						//加密关键字文件
						encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
						$plaintext.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
						//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
						$ciphertext=$plaintext;
						$cipherLen=strlen($ciphertext);
						$blockPlain="0-$cipherLen\t$att\r\n";
						fseek($fp,0,SEEK_END);
						fwrite($fp,$blockPlain."\r\n");
						fwrite($fp,$ciphertext);
					}
				}else{
					echo 'The file formats is error.';
				}

			/*********************写入新LST文件*********结束*****************/

			}else{//在ROOT或其他LST内上传文件
				if($dstFolder=='root'){
					$dpFolder='';
				}else{
					$dpFolder='root.lst.cpabe';
				}

				$fileContent=readBigFile($dstLstCpabeFile);
				if (!$fp = fopen($dstLstCpabeFile,'rb+')) {
					echo "Can`t open file:$rootLstCpabeFile.";
					exit;
				}
				if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\t)([a-zA-Z0-9]+)(\r\n)/i",$fileContent,$matches,PREG_OFFSET_CAPTURE)){
					//var_dump($matches);exit;
					$preg="/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i";
					if(preg_match_all($preg,$fileContent,$blockArr,PREG_SET_ORDER)){
						//var_dump($blockArr);exit;
						$blockNums=count($blockArr);//BLOCK的数量
					}else{
						$blockNums=0;
					}
					if($blockNums>0){
						//var_dump($blockArr);exit;
						//判断有无与属性相对应的HEADER部分
						//不完全匹配/(\d+)(\-){1}(\d+)(\t)(?:.*$att.*)(\r\n)/i
						if(preg_match_all("/(\d+)(\-){1}(\d+)(\t)(?:$att)(\r\n)/i",$fileContent,$outer)){
							//var_dump($blockArr);exit;
							//var_dump($outer);
							//echo 'aa';exit;
							//先正则找出匹配的header的属性部分
							if(preg_match("/(\d+)(\-){1}(\d+)(\t)([a-zA-Z0-9]+[a-zA-Z0-9 or]*)(\r\n)/i",$outer[0][0],$attrHeader)){
								$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
								if($attrHeader[5]==$blockArr[($lenHeaderArr-1)][5]){//如果匹配的header，是所有header中的最后一个，追加
									//$realname='XX33.DOC';
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个匹配的header的内容
									//echo $lastHeaderLine;exit;
									$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
									$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($dstLstCpabeFile,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$afterHeader=readFileFromOffset($dstLstCpabeFile,$pos2);//匹配的header内容之后的所有内容。//+2，是因为多了一个空行
									//echo $afterHeader;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;
									$keyArr=explode(' ',$keywords);
									$lenKeyword=count($keyArr);
									$strKeyword='';
									for($k=0;$k<$lenKeyword;$k++){
										if($keyArr[$k]){
											$strKeyword.=$keyArr[$k];
											if($k<($lenKeyword-1)){
												$strKeyword.='|';
											}
										}
									}
									$rndFile=generate_rand(8).'.keyword';
									$keyFilePath="$root/CPABE/$keyDir/$rndFile";
									$keyFileUrl="/CPABE/$keyDir/$rndFile";
									//把关键字集合保存到随机文件里
									createFile($keyFilePath,$strKeyword);
									//加密关键字文件
									encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
									$plainBlock.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
									//echo $plainBlock;exit;
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$plainBlock;//新的BLOCK的密文内容
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$newHeaderLine="$sectionA-$cipherLen\t$att\r\n";
									//echo $newHeaderLine;exit;
									$newContent=$beforeHeader.$newHeaderLine.$oldCipher.$ciphertext;
									//echo $newContent;exit;
									fclose($fp);
									createFile($dstLstCpabeFile,$newContent);
								}else{//插入
									//$realname='XX44.DOC';
									//var_dump($attrHeader);exit;
									$lenHeaderArr=count($blockArr);//更新header之前，匹配的header数量
									//var_dump($blockArr);exit;
									//获取匹配的header在所有HEADER中的index
									$index=0;
									for($i=0;$i<$lenHeaderArr;$i++){
										if($attrHeader[5]==$blockArr[$i][5]){
											$index=$i;
										}
									}
									//echo $index;exit;
									$indexHeaderLine=$blockArr[$index][0];//匹配的header的内容
									//echo $indexHeaderLine;exit;
									$pos1=strrpos($fileContent,$indexHeaderLine);//匹配的header的内容在LST文件中最后一次出现的位置。
									//echo $pos1;exit;
									$beforeHeader=readFileFromOffset($dstLstCpabeFile,0,$pos1);//匹配的header内容之前的所有内容
									//echo $beforeHeader;exit;
									$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//更新header之前，最后一个header的内容
									//echo $lastHeaderLine;exit;
									$pos2=strrpos($fileContent,$lastHeaderLine);//最后一个header的，后面内容的起始位置。
									$pos3=$pos2+strlen($lastHeaderLine);//最后一个header的，后面内容的起始位置。
									//echo $pos3;exit;
									$afterHeader=readFileFromOffset($dstLstCpabeFile,$pos3);//匹配的header内容之后的所有内容。包含一个空行。
									//echo $afterHeader;exit;
									$sectionA=$attrHeader[1];//区间A
									$sectionB=$attrHeader[3];//区间B
									//echo $sectionA.'@'.$sectionB;exit;
									$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
									//echo $lenCipherBlock;exit;
									$oldCipher=substr($afterHeader,0,$sectionA+2);//全部密文中，匹配header的相应密文的之前所有部分。+2，是因为要包含中间的空行
									//echo $oldCipher;exit;
									$cipherBlock=substr($afterHeader,$sectionA+2,$sectionB);//匹配header的相应密文的内容
									//echo $cipherBlock;exit;
									$lenCipherBlock1=strlen($cipherBlock);//更新前匹配header的相应密文的长度
									//@@@@@@@@明文，不用解密$plainBlock=decodeStrByLinux($cipherBlock,'uploadmanager');
									$plainBlock=$cipherBlock;
									$keyArr=explode(' ',$keywords);
									$lenKeyword=count($keyArr);
									$strKeyword='';
									for($k=0;$k<$lenKeyword;$k++){
										if($keyArr[$k]){
											$strKeyword.=$keyArr[$k];
											if($k<($lenKeyword-1)){
												$strKeyword.='|';
											}
										}
									}
									$rndFile=generate_rand(8).'.keyword';
									$keyFilePath="$root/CPABE/$keyDir/$rndFile";
									$keyFileUrl="/CPABE/$keyDir/$rndFile";
									//把关键字集合保存到随机文件里
									createFile($keyFilePath,$strKeyword);
									//加密关键字文件
									encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
									$plainBlock.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
									//echo $plainBlock;exit;
									//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plainBlock,$att.' or uploadmanager');//新的BLOCK的密文内容
									$ciphertext=$plainBlock;//新的BLOCK的密文内容
									$cipherLen=strlen($ciphertext);//新的BLOCK的长度
									//echo $cipherLen;exit;
									$diff=$cipherLen-$lenCipherBlock1;//更新密文后，与之前密文长度的差值，可能为正也可能为负
									//echo $diff;exit;
									//循环更新header区间和相应密文
									$nHeader='';
									$nCipher='';
									for($i=0;$i<$lenHeaderArr;$i++){
										$a='sa'.$i;
										$b='sb'.$i;
										$c='cipher'.$i;
										$t='attr'.$i;
										${$a}=$blockArr[$i][1];
										${$b}=$blockArr[$i][3];
										${$c}=substr($afterHeader,${$a}+2,${$b});
										//echo ${$c};exit;
										//echo $index;exit;
										if($i==$index){
											${$b}=$blockArr[$i][3]+$diff;
											${$c}=$ciphertext;
										}
										if($i>$index){
											${$a}=$blockArr[$i][1]+$diff;
										}
										if($i<$index){
											${$t}='';
										}else{
											${$t}=${$a}.'-'.${$b}."\t".$blockArr[$i][5]."\r\n";
										}
										$nHeader.=${$t};
										$nCipher.=${$c};
									}
									//echo $nHeader;exit;
									//echo $nCipher;exit;
									$newContent=$beforeHeader.$nHeader."\r\n".$nCipher;
									//echo $newContent;exit;
									fclose($fp);
									createFile($dstLstCpabeFile,$newContent);
								}
							}
						}else{//没有找到，先更新HEADER部分，然后再在文件尾部追加BLOCK
							//echo 'bb';exit;
							//先找到匹配BLOCK的Header部分的最后一个位置
								$lenHeaderArr=count($blockArr);//插入新header之前，匹配的header数量
								//var_dump($blockArr);exit;
								$lastHeaderLine=$blockArr[($lenHeaderArr-1)][0];//插入新header之前，最后一个匹配的header的内容
								//echo $lastHeaderLine;exit;
								$pos1=strrpos($fileContent,$lastHeaderLine);//获取最后一个匹配的header的内容在LST文件中最后一次出现的位置。
								$pos2=$pos1+strlen($lastHeaderLine);//最后一个匹配的header的内容，后面内容的起始位置。
									$beforeHeader=readFileFromOffset($dstLstCpabeFile,0,$pos2);//匹配的header内容之前的所有内容
								//echo $beforeHeader;exit;
								$afterHeader=readFileFromOffset($dstLstCpabeFile,$pos2);//匹配的header内容之后的所有内容。包含一个空行
								//echo $afterHeader;exit;
								$lenCipherBlock=strlen($afterHeader)-2;//插入新BLOCK之前，所有密文BLOCK的长度。-2，是因为$afterHeader包含最开始的一个空行，即多了\r\n
								//echo $lenCipherBlock;exit;

								//新BLOCK内容明文部分
								$plaintext='';
								//$realname='XX22.DOC';
								$plaintext.="DP\t$dpFolder\r\n";
								//$plaintext.="DC\t\t\r\n";
								$keyArr=explode(' ',$keywords);
								$lenKeyword=count($keyArr);
								$strKeyword='';
								for($k=0;$k<$lenKeyword;$k++){
									if($keyArr[$k]){
										$strKeyword.=$keyArr[$k];
										if($k<($lenKeyword-1)){
											$strKeyword.='|';
										}
									}
								}
								$rndFile=generate_rand(8).'.keyword';
								$keyFilePath="$root/CPABE/$keyDir/$rndFile";
								$keyFileUrl="/CPABE/$keyDir/$rndFile";
								//把关键字集合保存到随机文件里
								createFile($keyFilePath,$strKeyword);
								//加密关键字文件
								encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
								$plaintext.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
								//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
								$ciphertext=$plaintext;
								$cipherLen=strlen($ciphertext);
								$newHeaderLine="$lenCipherBlock-$cipherLen\t$att\r\n";
								//echo $cipherLen;exit;
								$newContent=$beforeHeader.$newHeaderLine.$afterHeader.$ciphertext;
								//echo $newContent;exit;
								fclose($fp);
								createFile($dstLstCpabeFile,$newContent);
						}
					}else{//LST内容为空的时候，直接在文件尾部开始插入BLOCK
						$plaintext='';
						//$realname='XXX.DOC';
						$plaintext.="DP\t$dpFolder\r\n";
						//$plaintext.="DC\t\t\r\n";
						$keyArr=explode(' ',$keywords);
						$lenKeyword=count($keyArr);
						$strKeyword='';
						for($k=0;$k<$lenKeyword;$k++){
							if($keyArr[$k]){
								$strKeyword.=$keyArr[$k];
								if($k<($lenKeyword-1)){
									$strKeyword.='|';
								}
							}
						}
						$rndFile=generate_rand(8).'.keyword';
						$keyFilePath="$root/CPABE/$keyDir/$rndFile";
						$keyFileUrl="/CPABE/$keyDir/$rndFile";
						//把关键字集合保存到随机文件里
						createFile($keyFilePath,$strKeyword);
						//加密关键字文件
						encryptFileByLinux($keyFilePath,$att.' or uploadmanager');
						$plaintext.="F\t$realname\t$random.file.cpabe\t$att\t$username\t$keyFileUrl.cpabe\r\n";
						//@@@@@@@@不加密$ciphertext=encodeStrByLinux($plaintext,$att.' or uploadmanager');
						$ciphertext=$plaintext;
						$cipherLen=strlen($ciphertext);
						$blockPlain="0-$cipherLen\t$att\r\n";
						fseek($fp,0,SEEK_END);
						fwrite($fp,$blockPlain."\r\n");
						fwrite($fp,$ciphertext);
					}
				}else{
					echo 'The file formats is error.';
				}
			
			}


		/***************处理部分结束*************************/	
		
		echo '<b>The file has been encrypted.</b><br>';
		echo '</p>';
	}
}
if($action=='KeyGen'){
	include('keygen.php');
}
?>
</div>


</body>
</html>