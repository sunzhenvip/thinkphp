<?php
namespace Admin\Controller;
use Think\Controller;
class TestController extends Controller 
{
    public function piao()
    {
    	$db = M();
    	$db->execute('LOCK TABLE test.a WRITE');
    	// 先取出剩余的票数
    	$sql = 'select id from test.a';
    	$piao = $db->query($sql);
    	if($piao[0]['id'] > 0)
    	{
    		file_put_contents('./Piao/a'.uniqid(), '');
    		$db->execute('UPDATE test.a SET id=id-1');
    	}

    	$this->display();
    	
    }
}