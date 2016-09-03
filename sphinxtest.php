<?php
header('Content-Type:text/html;charset=utf-8');
require './sphinxapi.php';
$sphinx = new SphinxClient();
// 要查询的SPHINX 中的索引
$indexName = 'songs';
// 要查询的短语
$key = '冬天里的一把火';
// 连接sphinx服务器
$sphinx->setServer('localhost', 9312);
// 只要有任何一个词就可以搜索出来
$sphinx->setMatchMode(SPH_MATCH_ANY);
// 查询sphinx返回查询到的歌曲的ID
// 参数一：要搜索的关键字  参数二、从哪个索引里面搜索
$ret = $sphinx->query($key, $indexName);

// 解析出记录的ID
if(isset($ret['matches']))
{

	echo '关键词：'.$key.'  耗时：'.$ret['time'].'秒 共返回出：'.$ret['total'].'条记录<hr />';
	$ids = array_keys($ret['matches']);
	$ids = implode(',', $ids);
	mysql_connect('localhost', 'root', '');
	mysql_query('SET NAMES UTF8');
	mysql_select_db('test');
	$rs = mysql_query('SELECT * FROM curl_songs WHERE id IN('.$ids.') AND title <> ""');
	while($row = mysql_fetch_assoc($rs))
	{
		// 处理记录把词高亮显示 --> 返回的是索引数组
		$row1 = $sphinx->buildExcerpts($row, $indexName, $key, array(
				'before_match' => '<font style="color:#F00;font-weight:bold;">',
				'after_match' => '</font>',
			));
		echo $row1[1].'   作者：'.$row1[2].'<hr />';
		echo $row1[3].'<br /><br />';
	}
}
else
	echo 'not found !';