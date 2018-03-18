<?php
namespace Api\Controller;
use Think\Controller;
class MeetController extends Controller {
	public function _initialize(){
		$this -> company = M('Company');
        $this -> meetfcla = M('Meetforclass');
        $this -> mc = M('Meetcompany');
        $this -> mu = M('Meetuser');

	}

    public function addcomp(){
        $data = array(
            'name' => $_POST['name'],
            'tel' => $_POST['tel']
            );
        $has = $this -> company -> where($data) -> limit(1) -> select();
        if (empty($has)) {
            $data['people'] = $_POST['people'];
            $data['position'] = $_POST['position'];
            $data['email'] = $_POST['email'];
            $data['ctime'] = time();
            $data['utime'] = time();
            $cid = $this -> company -> add($data);
        }else{
            $cid = $has[0]['id'];
        }
        $mc = array(
            'cid' => $cid,
            'mid' => $_POST['mid'],
            'ctime' => time(),
            'utime' => time(),
            'status' => 0
            );

        $res = $this -> mc -> add( $mc );
        if (!empty($res)) {
            apiResponse("success","申请报名成功，等待管理员审核！",$res);
        }else{
            apiResponse("error","申请失败！");
        }
    }
    

    function apiResponse($flag = 'error', $message = '',$data = array()){
        $result = array('flag'=>$flag,'message'=>$message,'data'=>$data);
        print json_encode($result);exit;
    }
}