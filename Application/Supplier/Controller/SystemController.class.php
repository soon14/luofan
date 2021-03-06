<?php
namespace Supplier\Controller;
use Supplier\Common\Controller\CommonController;
class SystemController extends CommonController {
	public function _initialize(){
        parent::_initialize();
        $this->assign("munetype" ,strtolower(CONTROLLER_NAME));
        $this->assign("urlname"  ,strtolower(ACTION_NAME));
    }


    public function index(){
    	$m = M("system");
    	$cache = $m->where(array("id"=>1))->find();

    	$cache["lastadmin"] = M("user")->where(array("id"=>$cache['lastadmin']))->getField("username");
		dump($cache);
    	$this->assign("cache",$cache);
        $this->assign("urlname","index");
    	$this->display();
    }

    public function editApp(){
    	$m = M("system");
    	$data = I("post.");
    	$data["lastAdmin"] = $_SESSION["supplier_id"];
    	$data["lastTime"]  = date("Y-m-d H:i:s",time());
    	$res = $m->where("id=1")->save($data);
    	if($res===false){
    		$info["status"] = 0;
    		$info["info"]   = "修改失败";
    	}else{
    		$info["status"] = 1;
    		$info["info"]   = "修改成功，请妥善保管";
    	}
    	$this->ajaxReturn($info);
    }

    public function adminlist(){
        $cate= M("admin_cate")->where(array('per_name'=>'供应商管理'))->order('id asc')->find();
        $cache = M("user")->field('a.*,b.per_name')
        		->alias('a')->join('LEFT JOIN app_admin_cate as b ON a.cate=b.id')
                ->where(array('a.pid'=>$cate['id']))
        		->order('a.cate asc,add_time asc')
        		->select();

        $where['id']=array('gt',$cate['id']);
        $this->assign("cate",$cate);
        $cate_list = M("admin_cate")->where($where)->select();
        /*echo M("admin_cate")->getLastSql();*/
        $this->assign("cache",$cache);
        $this->assign("cate_list",$cate_list);
        $this->assign("urlname","admin");
        $this->display('system/admin');
    }



    public function updatepwd(){
        $action=D('User');
        $pass=I('post.password');
        if($pass){
            $md5_pass=md5($pass);
            $re=$action->where("username='".$_SESSION['supplier_name']."'")->setField('password',$md5_pass);
            if($re){
                $this->success('修改成功',U('/Admin/System/updatepwd'));die;
            }else{   
                $this->error('修改失败');die;
            }
        }
        $this->assign('munetype',9);
        $this->display('updatepwd');
    }
    
    public function administrator(){
        $action=D('User');
        $user_list = $action->user_list();
        $m = M('admin_cate');
        foreach($user_list as $key => $value)
        {
            $catename = $m->field('per_name')->where('id='.$value['cate'])->find();
            $user_list[$key]['catename'] = $catename['per_name'];
        }
        
        $cate_list = $m->field('id,per_name')->select();
        
        $this->assign('user_list',$user_list);
        $this->assign('cate_list',$cate_list);
        $this->display();
    }
    
    public function addAdmin(){
        $username=I('post.username');
        $password=I('post.password');
        $cate=I('post.cate');
        $country=I('post.country');
        $pid=I('post.pid')?I('post.pid'):10;
        $action = D('User');
        $rs = $action->addAdmin($username,$password,$cate,$country,$pid);
        if($rs==0)
        {
            $data['info']   =   '账号已存在'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        elseif($rs==2)
        {
            $data['info']   =   '添加失败'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        elseif($rs==1)
        {
            $data['info']   =   '添加成功'; // 提示信息内容
            $data['status'] =   1;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        else
        {
            $data['info']   =   '未知错误'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        
        $this->ajaxReturn($data);
        return;
        
    }
    

    public function editAdmin(){
        $username = I('post.username');
        $password = I('post.password');
        $cate = I('post.cate');
        $id = I('post.id');
        $country = I('post.country');

        $action=D('User');
        $rs=$action->editAdmin($id,$username,$password,$cate,$country);
        if($rs==0)
        {
            $data['info']   =   '账号不存在'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        elseif($rs==2)
        {
            $data['info']   =   '修改失败'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        elseif($rs==1)
        {
            $data['info']   =   '修改成功'; // 提示信息内容
            $data['status'] =   1;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }
        else
        {
            $data['info']   =   '未知错误'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
        }

        $this->ajaxReturn($data);
        return;
    }
    
    public function ableAdmin(){

        $id = I('post.id');
        $able=I('post.able');
        $action=D('User');
        $rs=$action->able($id,$able);
        if($rs)
        {
            $data['info']   =   '修改成功'; // 提示信息内容
            $data['status'] =   1;  // 状态 如果是success是1 error 是0
        }
        else
        {
            $data['info']   =   '修改失败'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
        }
        $this->ajaxReturn($data);
        return;
    }

    // 删除队员
    public function deladmin(){

        $id=I('post.id');
        $action = D("User");
        $rs = $action->del($id);
        if($rs)
        {
            $data['info']   =   '删除成功'; // 提示信息内容
            $data['status'] =   1;  // 状态 如果是success是1 error 是0
        }
        else
        {
            $data['info']   =   '删除失败'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
        }
        $this->ajaxReturn($data);
        return;
    }

    //  节点列表
    public function nodeList(){
        $m = M("admin_node");
        $nodes = $m->where(array("level"=>1))->order("sort asc")->select();
        foreach($nodes as $k=>$v){
            $nodes2 = $m->
                where(array("level"=>2,"pid"=>$v['id']))->order("sort asc")->
                select();
            $nodes[$k]["nodes"] = $nodes2;
            foreach($nodes2 as $kk=>$vv){
                $nodes3 = $m->
                    where(array("level"=>3,"pid"=>$vv['id']))->order("sort asc")->
                    select();
                $nodes[$k]["nodes"][$kk]['nodes'] = $nodes3;

                foreach($nodes3 as $kkk=>$vvv){
                    $nodes4 = $m->
                        where(array("level"=>4,"pid"=>$vvv['id']))->order("sort asc")->
                        select();
                    $nodes[$k]["nodes"][$kk]['nodes'][$kkk]['nodes'] = $nodes4;
                }
            }
        }
        $this->assign("cache", $nodes);
        $this->display();
    }

    // 新增节点
    public function addNode(){
        if(IS_AJAX){
            $data['pid']        = I("post.pid");
            $data['classname']  = I("post.classname");
            $data['sort']       = I("post.sort");
            $data['controller'] = strtolower(I("post.controller"));
            $data['action']     = strtolower(I("post.action"));
            $m = M("admin_node");
            if($data['pid']){
                $p = $m->find( $data['pid'] );
                if( !$p ){
                    $this->ajaxReturn(array("status"=>0, "info"=>"创建失败！"));
                }
                $data['level'] = $p['level']+1;
                $data['pid']   = $p['id'];
                $data['pid2']  = $p['pid'];
                $data['pid3']  = $p['pid2'];
            }else{
                $data['level'] = 1;
                $data['pid']   = 0;
                $data['pid2']  = 0;
                $data['pid3']  = 0;
            }
            $res = $m->add($data);
            if($res){
                $this->ajaxReturn(array("status"=>1, "info"=>"创建成功！"));
            }
            $this->ajaxReturn(array("status"=>0, "info"=>"创建失败！"));
        }
    }

    // 编辑节点
    public function editNode(){
        if(IS_AJAX){
            $id                 = I("post.id");
            $data['classname']  = I("post.classname");
            $data['sort']       = I("post.sort");
            $data['controller'] = strtolower(I("post.controller"));
            $data['action']     = strtolower(I("post.action"));
            $m = M("admin_node");
            if(!$id){
                $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
            }
            $res = $m->where(array('id'=>$id))->save($data);
            if($res){
                $this->ajaxReturn(array("status"=>1, "info"=>"修改成功！"));
            }
            $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
        }
    }

    // 删除节点
    public function delNode(){
        $id = I("id");
        if(!$id){
            $this->error("删除失败！");
        }
        $m = M("admin_node");
        $m->where(array("id"=>$id))->delete();
        $m->where(array("pid"=>$id))->delete();
        $m->where(array("pid2"=>$id))->delete();
        $m->where(array("pid3"=>$id))->delete();
        $this->success("删除成功！");
    }

    // 权限列表 ok
    public function roleList(){
        $cache = M("admin_cate")->select();
        $this->assign("cache", $cache);
        $this->display();
    }

    // 权限设置
    public function roleSet(){
        $id = I('id');
        // $id = 1;
        $node_arr = M("admin_cate")->find($id);
        if(!$node_arr){
            $this->error();
        }
        $per_name = $node_arr['per_name'];
        $node_arr = $node_arr['module'];
        $node_arr = explode(",",  $node_arr);
        $this->assign("node_arr", $node_arr);
        $this->assign("per_name", $per_name);
        $this->assign("id",       $id);
        $this->nodeList();
    }

    // 编辑权限节点
    public function addRoleNode(){
        $ids = I("post.ids");
        $id  = I("post.id");
        if(!$id){
            $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
        }
        $ids = implode(",", array_filter(explode(",", $ids)));
        $res = M("admin_cate")->where(array('id'=>$id))->setField("module", $ids);
        if($res){
            $this->ajaxReturn(array("status"=>1, "info"=>"修改成功！"));
        }else{
            $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
        }
    }

     // 添加权限
    public function addRole(){
        $per_name = trim(I("per_name"));
		$m=M("admin_cate");
        if(!$per_name){
            $this->ajaxReturn(array("status"=>0, "info"=>"添加失败！"));
        }
		
		$res_c = $m->where(array("per_name"=>$per_name))->count();
		if($res_c){
            $this->ajaxReturn(array("status"=>0, "info"=>"群组名称已存在！"));
        }
		
        $res = $m->add(array("per_name"=>$per_name));
        if($res){
            $this->ajaxReturn(array("status"=>1, "info"=>"添加成功！"));
        }else{
            $this->ajaxReturn(array("status"=>0, "info"=>"添加失败！"));
        }
    }

    // 修改权限名
    public function editRole(){
        $id = I("post.id");
        $per_name = trim(I("post.per_name"));
        if(!$id || !$per_name){
            $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
        }
        $res = M("admin_cate")->where(array('id'=>$id))->save(array("per_name"=>$per_name));
        if($res){
            $this->ajaxReturn(array("status"=>1, "info"=>"修改成功！"));
        }else{
            $this->ajaxReturn(array("status"=>0, "info"=>"修改失败！"));
        }
    }

    // 删除群组权限
    public function delRole(){
        $id = I("id");
        if(!$id){
            $this->ajaxReturn(array("status"=>0, "info"=>"删除失败！"));
        }
        $res = M("admin_cate")->delete($id);
        if($res){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
}