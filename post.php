<!--Author: Zhi Create date:2021/7/4-->
<?php
set_time_limit(0);
include('function.php');
header("Content-type: text/html; charset=utf-8");

print str_repeat(" ", 4096);//php.ini output_buffering默认是4069字符或者更大，即输出内容必须达到4069字符服务器才会flush刷新输出缓冲

$loop=2;//循环POST次数
for($i=0;$i<$loop;$i++){
	$config=paraConfig();
	//var_dump($config);exit;
	if($config){
		curlPost($config['username'],$config['password'],$config['loginUrl'],$config['uploadUrl'],$config['post_data']);
	}
	echo ($i+1);
    ob_flush();
    flush();
    sleep(1);
}

exit;


//配置参数并返回，便于循环
function paraConfig(){
	$root=$_SERVER['DOCUMENT_ROOT'];

	$filesDir=$root.'/files';//要上传的文件夹
	$oFile=array();//要上传的文件列表
	if (is_dir($filesDir)) {
		if ($dh = opendir($filesDir)) {
			while (($file = readdir($dh)) !== false) {
				//echo "filename: $file : filetype: " . filetype($filesDir.'/'.$file).'<br  />';
				if($file!='.'&& $file!='..'){
					$oFile[]=$filesDir.'/'.$file;
				}
			} closedir($dh);
		}
	}
	$fRand=mt_rand(0,count($oFile)-1);
	$randFile=$oFile[$fRand];//随机抽取一个文件
	//echo $randFile;exit;

	$wordList=array('day','sky','nose','ear','eye','body','face','hand','cap','cat','hat','dog','wire','down','left','left','tea','food','taxi','moon');
	$kRand=mt_rand(1,5);//最多为五个关键词
	$randKeywordsArr=array();//随机关键词数组
	for($i=0;$i<$kRand;$i++){
		$wRand=mt_rand(0,count($wordList)-1);
		$randKeyword=$wordList[$wRand];//随机抽取一个关键词
		$randKeywordsArr[]=$randKeyword;
	}
	array_unique($randKeywordsArr);//过滤重复关键词后的新数组
	sort($randKeywordsArr);//重新排序
	//var_dump($randKeywordsArr);exit;
	$lenKeywords=count($randKeywordsArr);
	$randKeywords='';//1-5个随机关键词组成的字符串
	//把关键词数组转成字符串
	for($i=0;$i<$lenKeywords;$i++){
		$randKeywords.=$randKeywordsArr[$i];
		if($i<($lenKeywords-1)){
			$randKeywords.=' ';
		}
	}
	//echo $randKeywords;exit;

	$attrList=array('minister','master','member','minister or master','minister or member','master or member','minister or master or member');
	$tRand=mt_rand(0,count($attrList)-1);
	$randAttr=$attrList[$tRand];//随机抽取一个属性
	//echo $randAttr;exit;


	//文件夹数组
	$dirArr=array(
		array(
			"dirName"=>"ROOT",
			"dstFolder"=>"root",
		),
		array(
			"dirName"=>"AAA",
			"dstFolder"=>"GVdcJjJs",
		),
		array(
			"dirName"=>"BBB",
			"dstFolder"=>"4LIM1Zhj",
		),
		array(
			"dirName"=>"CCC",
			"dstFolder"=>"RM8A3u9O",
		),
		array(
			"dirName"=>"DDD",
			"dstFolder"=>"CyBfaCqN",
		),
		array(
			"dirName"=>"EEE",
			"dstFolder"=>"ehd0ndWv",
		)
	);
	$dRand=mt_rand(0,count($dirArr)-1);
	$randDir=$dirArr[$dRand];//随机抽取一个文件夹
	$rDirname=$randDir['dirName'];
	$rDstFolder=$randDir['dstFolder'];
	//echo $rDstFolder;exit;

	//用户数组
	$userArr=array(
		array(
			"username"=>"aaa",
			"password"=>"111111",
		),
		array(
			"username"=>"bbb",
			"password"=>"111111",
		),
		array(
			"username"=>"ccc",
			"password"=>"111111",
		)
	);
	$uRand=mt_rand(0,count($userArr)-1);
	$randUser=$userArr[$uRand];//随机抽取一个用户
	$rUsername=$randUser['username'];
	$rPassword=$randUser['password'];
	//echo $rUsername;exit;

	//$username='aaa';
	//$password='111111';
	$username=$rUsername;
	$password=$rPassword;
	$loginUrl='http://10.20.22.150/hello/fm.php?action=login';
	$post_data = array (
		"att" => $randAttr,
		"dirName" => $rDirname,
		"dstFolder" => $rDstFolder,
		"keywords" => $randKeywords,
		// 要上传的本地文件地址
		"myfile" => "@".$randFile
	);
	$uploadUrl = "http://10.20.22.150/hello/userUploadFile.php?action=upload&dn=ROOT&dir=root";

	$configArr=array();
	$configArr['username']=$username;
	$configArr['password']=$password;
	$configArr['loginUrl']=$loginUrl;
	$configArr['uploadUrl']=$uploadUrl;
	$configArr['post_data']=$post_data;
	return $configArr;
}
//CURL上传
function curlPost($username,$password,$loginUrl,$uploadUrl,$post_data){
	$cookie_path = './';
	$vars['name'] = $username;
	$vars['password'] = $password;
	$method_post = true;

	$ch = curl_init();
	$params[CURLOPT_URL] = $loginUrl;
	$params[CURLOPT_HEADER] = true;
	$params[CURLOPT_RETURNTRANSFER] = true;
	$params[CURLOPT_FOLLOWLOCATION] = false;
	$params[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';

	$postfields = '';
	foreach ($vars as $key => $value){
		$postfields .= urlencode($key) . '=' . urlencode($value) . '&';
	}
	$params[CURLOPT_POST] = true;
	$params[CURLOPT_POSTFIELDS] = $postfields;

	if (isset($_COOKIE['cookie_jar']) && ($_COOKIE['cookie_jar'] || is_file($_COOKIE['cookie_jar'])))
	{
		$params[CURLOPT_COOKIEFILE] = $_COOKIE['cookie_jar'];
	}
	else
	{
		$cookie_jar = tempnam($cookie_path, 'cookie');
		$params[CURLOPT_COOKIEJAR] = $cookie_jar;
		setcookie('cookie_jar', $cookie_jar);
	}
	curl_setopt_array($ch, $params);
	$content = curl_exec($ch);

	//echo $content;

	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
	curl_setopt($ch, CURLOPT_URL, $uploadUrl);
	//execute post
	$result = curl_exec($ch) ;
	//close connection
	curl_close($ch) ;
}

?>