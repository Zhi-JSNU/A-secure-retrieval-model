<!--Author: Zhi Create date:2021/7/9-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search Files</title>
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
.suggestion-item {
    padding: 5px;
    cursor: pointer;
    font-size: 14px;
    width: 292px;
    margin-left: 291px;
    border: 1px solid #000;
    line-height: 1
}
.suggestion-item:hover {
    background-color: #add8e6; /* 修改为浅蓝色背景 */
}
</style>
<script type="text/javascript" src="./js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("select[name='dstFolder']").change(function(){
            if($(this).val()=='0'){
                $("input[name='folder']").show();
                $("div[name='inFolder']").show();
                $("input[name='folder']").focus();
            } else {
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

$ta=getMillisecond();
//echo '本页面开始时间：'.$ta.'<br />';

if (empty($_SESSION["name"])) {
	header("location:fm.php");
	exit;
}
$suggestions=array();
$username=strval($_SESSION["name"]);
//echo $username;
$attribute=strval($_SESSION['attribute']);
//echo $attribute;
$action=isset($_GET['action'])?strval($_GET['action']):'';

//$keyword = isset($_GET['query']) ? $_GET['query'] : '';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

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
echo '<br />';

$root=$_SERVER['DOCUMENT_ROOT'];
$upDir='files';
$lstDir='lstFiles';
$rootLstFile="$root/CPABE/$lstDir/root.lst";
$rootLstCpabeFile="$root/CPABE/$lstDir/root.lst.cpabe";
$dir=isset($_GET["dir"])?strval($_GET["dir"]):'root';
$dn=isset($_GET["dn"])?strval($_GET["dn"]):'ROOT';

//$newLstPath="$root/CPABE/$lstDir/$newFloder.lst.cpabe";
$folders=getFoldersByAttr($rootLstCpabeFile,$attribute);
$cpabes=getCpabesByAttr($rootLstCpabeFile,$attribute);
//echo getArrayTree($folders,0);exit;
$strFolder=substr(getArrayString($folders),0,-1);
$strCpabe=substr(getArrayString($cpabes),0,-1);
$arrFolder=explode(',',$strFolder);
$arrCpabe=explode(',',$strCpabe);
$lenArr=count($arrFolder);
$lstOptions='';
for($i=0;$i<$lenArr;$i++){
	if($dstFolder==$arrCpabe[$i]){
		$selected=" selected";
	}else{
		$selected='';
	}
	$lstOptions.='<option value='.$arrCpabe[$i].$selected.'>'.$arrFolder[$i].'</option>';
}

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
	<h1>The File Search System</h1>
	<?php
	if(!empty($dn) && !empty($dir)){
		echo '<form method="post" name="upFile" action="?action=search&dn='.$dn.'&dir='.$dir.'" enctype="multipart/form-data">';
	}else{
		echo '<form method="post" name="upFile" action="?action=search" enctype="multipart/form-data">';
	}
	?>
		Input the keyword：<input type="text" name="keyword" value="<?=$keyword?>"><br />
        Please choose the folder that will be searched:
		<select name="dstFolder">
			<option value="root"<?php	if($dstFolder=='root'){echo ' selected';} ?>>Root</option>
			<?=$lstOptions?>
		</select><br />
		<div name="inFolder"<?php if($dstFolder!='0'){echo ' style="display:none;"';} ?>>Input the new folder name:<input type="text" name="folder" /><br /></div>
		<br /><input type="submit" value="start search file">
	</form>
</div>
<p class="rtFM"><a href="fm.php">click to return</a></p>

<p>
<?php
?>

</p>

<div class="taCenter">
<?php
	$tb=getMillisecond();
	//echo '本页面结束时间：'.$tb.'<br />';
	echo '<br />the cost of loading page：'.round(($tb-$ta)/1000,3).' s';
if($action=='search'){
	$sTime=getMillisecond();
	//var_dump(empty($keyword) && strlen($keyword)==0);exit;
	//var_dump($keyword);exit;
	if($dstFolder=='0' && empty($folder)){
		echo '<p class="alarm"><b>Please input the folder name.</b></p>';exit;
	}
	/*
	if(empty($readpermission)){
		echo '<p class="alarm"><b>Please input the readpermission.</b></p>';exit;
	}
	*/
	if(empty($keyword) && strlen($keyword)==0){
		echo '<p class="alarm"><b>Please input the keyword.</b></p>';exit;
	}
	//echo $dstFolder;exit;
	$root=$_SERVER['DOCUMENT_ROOT'];
	$upDir='files';
	$lstDir='lstFiles';
	$rootLstFile="$root/CPABE/$lstDir/root.lst";
	$rootLstCpabeFile="$root/CPABE/$lstDir/root.lst.cpabe";

	//echo $dstFolder;exit;
	$dstLstCpabeFile="$root/CPABE/$lstDir/$dstFolder.lst.cpabe";

	$dstFolders=getFoldersByAttr($dstLstCpabeFile,$attribute);
	$dstCpabes=getCpabesByAttr($dstLstCpabeFile,$attribute);
	//print_r($dstFolders);exit;
	//echo getArrayTree($dstFolders,0);exit;
	$strDstFolder=substr(getArrayString($dstFolders),0,-1);
	$strDstCpabe=substr(getArrayString($dstCpabes),0,-1);
	$arrDstFolder=explode(',',$strDstFolder);
	$arrDstCpabe=explode(',',$strDstCpabe);
	$newDstFolder=array();
	$newDstCpabe=array();

    foreach ($arrDstFolder as $key => $val) {
        if (empty($val)) {
            continue;
        }
        $newDstFolder[] = $val;
    }
    foreach ($arrDstCpabe as $key => $val) {
        if (empty($val)) {
            continue;
        }
        $newDstCpabe[] = $val;
    }
	array_unshift($newDstCpabe,$dstFolder);
	$lenDstArr=count($newDstCpabe);

	$files='';
	for($i=0;$i<$lenDstArr;$i++){
		$lstCpabeFile="$root/CPABE/$lstDir/$newDstCpabe[$i].lst.cpabe";
//        var_dump($lstCpabeFile);
		//echo $lstCpabeFile;
		//echo $keyword;
		$temStr=getFilesByAttrAndKeyword($lstCpabeFile,$attribute,$keyword);
//        var_dump($temStr);
		//if($temStr)echo $temStr;
		//else echo "123455";
		if($temStr && $temStr!='Not Found.'){
			$files.=$temStr;
		}
	}

	if(empty($files)){
		$files='Not found.';
	}

	$eTime=getMillisecond();
	$diffTime=$eTime-$sTime;
	echo '<br />';
	echo 'The cost of this search：<font color="#9B30FF"><b>'.round($diffTime/1000,3).' s</b></font><br /><br />';
	echo '<div class="files" style="margin-top:20px;">'.$files.'</div>';


}
?>
</div>


</body>
</html>
