<?php
namespace Api\Controller;
use Think\Controller;
class LoginController extends Controller {
	
	//获取用户 openid
    public function getOpenid(){
    	$js_code = $_POST['js_code'];
    	$appId = "wxe900513990933078";
    	$appKey = "8844c389cef08accd7ea86abfffbfb7e";
    	$url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appKey&js_code=$js_code&grant_type=authorization_code";
    	$obj = json_decode($this -> curl("",$url),true);
    	$this -> authlogin($obj->openid);
    }

    public function curl($data,$url){
        $ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$headers = array(
        	'content-type:'.'application/json'
        	);
		curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
        return $file_contents;
    }

	//用户登录
	public function authlogin($openid){
		//$openid = $_POST['openid'];
		if (!$openid) {
			echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
			exit();
		}
		$con = array();
		$con['openid']=trim($openid);
		$userinfo = M('user')->where($con)->find();
		$uid = $userinfo['id'];
		if ($uid) {
			$save['num'] = $userinfo['num'] +1;
			$save['utime'] = time();
			$changenum = M('user')->where('id='.intval($uid))->save($save);
			echo json_encode(array('status'=>1,'arr'=>$userinfo));
			exit();
		}else{
			$data = array();
			$data['name'] = $_POST['nickName'];
			$data['nickname'] = $_POST['nickName'];
			$data['headpic'] = $_POST['avatarUrl'];
			$data['sex'] = $_POST['gender'];
			$data['city'] = $_POST['city'];
			$data['num'] = 0;
			$data['status'] = 0;
			$data['openid'] = $openid;
			$data['ctime'] = time();
			$data['utime'] = time();
			if (!$data['openid']) {
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
			$res = M('user')->add($data);
			if ($res) {
				$err = array();
				$err['ID'] = intval($res);
				$err['name'] = $data['name'];
				$err['headpic'] = $data['headpic'];
				$err['openid'] = $openid;
				echo json_encode(array('status'=>1,'arr'=>$err));
				exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
		}
	}
}