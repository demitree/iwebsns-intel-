<?php
function getAppId(){
  if(isset($_GET['app'])){
  	  return $_GET['app'];
  }else{
  	  return 'start';
  }
}

function getActId(){
  if(isset($_GET['act'])){
  	  return $_GET['act'];
  }else{
  	  echo 'para err';
  	  exit;
  }
}
function request_uri2()
{
    if (isset($_SERVER['REQUEST_URI']))
    {
        $uri = $_SERVER['REQUEST_URI'];
    }
    else
    {
        if (isset($_SERVER['argv']))
        {
            $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
        }
        else
        {
            $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
    }
    return $uri;
}
function getRequest(){
	global $siteDomain;
	global $urlRewrite;
	$server_url=str_replace(array(".html",".htm"),"",request_uri2());
	if($urlRewrite==2 && !strpos($server_url,'?')){
		$request_str=strstr($server_url,'.php');
		$request_arr=explode('/',$request_str);
		array_shift($request_arr);
		return $request_arr;
	}else{
		return '?'.$_SERVER['QUERY_STRING'];
	}
}

function getReUrl(){
	$urlParaStr='';
	$request_arr=getRequest();
	if(is_string($request_arr)){//没有伪静态
		$urlParaStr=str_replace('h=','user_id=',$request_arr);
		if(!strpos(",".$urlParaStr,'app')){
			$urlParaStr=$urlParaStr.'&app=hstart';
		}
	}else{//伪静态
		if(isset($request_arr[1])){
			foreach($request_arr as $val){
				$urlParaStr.='/'.$val;
			}
		}else if(isset($request_arr[0])){
			$urlParaStr='/'.$request_arr[0].'/hstart';
		}
	}
	return $urlParaStr;
}

function transRewrite(){
	$rewrite_array=array("blog","photo");
	$script_name = basename($_SERVER['SCRIPT_NAME']);
	$request_str=getReUrl();
		
	if(strpos(','.$request_str,'/')){
		$request_arr=explode('/',$request_str);
		array_shift($request_arr);

		if($script_name=='home.php'||intval($request_arr[0])){
			isset($request_arr[0]) && $_GET['user_id']=$_GET['h']=$request_arr[0];
			isset($request_arr[1]) && $_GET['app']=$request_arr[1];
			$app=$request_arr[1];
		}else if($script_name=='modules.php' && in_array($request_arr[0],$rewrite_array)){
			isset($request_arr[0]) && $_GET['app']=$request_arr[0];
			$app=$request_arr[0];
		}
		if(isset($app)){
			switch($app){
				case "hstart":
				isset($request_arr[0]) && $_GET['user_id']=$request_arr[0];
				isset($request_arr[1]) && $_GET['app']=$request_arr[1];
				break;
				case "blog":
				if($script_name=='home.php'||intval($request_arr[0])){
					isset($request_arr[0]) && $_GET['user_id']=$_GET['h']=$request_arr[0];
					isset($request_arr[1]) && $_GET['app']=$request_arr[1];
					isset($request_arr[2]) && $_GET['id']=$request_arr[2];				
				}else if($script_name=='modules.php'){
					isset($request_arr[0]) && $_GET['app']=$request_arr[0];
					isset($request_arr[1]) && $_GET['id']=$request_arr[1];
					isset($request_arr[2]) && $_GET['user_id']=$_GET['h']=$request_arr[2];
				}
				break;
				case "photo":
				if($script_name=='home.php'||intval($request_arr[0])){
					isset($request_arr[0]) && $_GET['user_id']=$_GET['h']=$request_arr[0];
					isset($request_arr[1]) && $_GET['app']=$request_arr[1];
					isset($request_arr[2]) && $_GET['photo_id']=$request_arr[2];
					isset($request_arr[3]) && $_GET['album_id']=$request_arr[3];
				}else if($script_name=='modules.php'){
					isset($request_arr[0]) && $_GET['app']=$request_arr[0];
					isset($request_arr[1]) && $_GET['photo_id']=$request_arr[1];
					isset($request_arr[2]) && $_GET['album_id']=$request_arr[2];
					isset($request_arr[3]) && $_GET['user_id']=$_GET['h']=$request_arr[3];
				}
				break;
				case "community_space":
				if($script_name=='modules.php'){
					isset($request_arr[0]) && $_GET['app']=$request_arr[0];
					isset($request_arr[1]) && $_GET['comm_id']=$request_arr[1];
					isset($request_arr[2]) && $_GET['user_id']=$request_arr[2];
				}
				break;
			}
		}
	}
}

function rewrite_fun($url){
	global $urlRewrite;
	if($urlRewrite){
		$url_type_str='/';
		if($urlRewrite==2){
			$url_type_str='.php/';
		}
		$str_url=str_replace(array("?","&"),"/",strstr($url,'.php?'));
		$url_array=explode('/',$str_url);
		$result_url='';
		foreach($url_array as $rs){
			$result_url.=strstr($rs,'=');
		}
		$result_url=strtr($result_url,'=','/');
		if(substr($result_url,0,1)=='/'){
			$result_url=substr($result_url,1);
		}
		if(strpos(','.$url,'home.php')){
			$result_url='home'.$url_type_str.$result_url;
		}else if(strpos(','.$url,'modules.php')&&$urlRewrite==2){
			$result_url='modules'.$url_type_str.$result_url;
		}
		return $result_url;
	}else{
		return $url;
	}
}

if(isset($urlRewrite)==2){
	transRewrite();
}
?>