<?php
$userArr=array(
	array(
		"username"=>"aaa",
		"password"=>"111111",
		"attribute"=>"minister"
	),
	array(
		"username"=>"bbb",
		"password"=>"111111",
		"attribute"=>"master"
	),
	array(
		"username"=>"ccc",
		"password"=>"111111",
		"attribute"=>"member"
	)
);

//echo $userArr[1]["username"];exit;
//var_dump($userArr);exit;
$permissionArr=array();
//var_dump($permissionArr);exit;
$configArr=array($userArr,$permissionArr);
//var_dump($configArr);exit;
return $configArr;

?>