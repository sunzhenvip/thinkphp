<?php
mysql_connect('localhost', 'root', '');
mysql_select_db('test');
mysql_query('lock table a write');
$sql = 'select id from a';
$rs = mysql_query($sql);
$row = mysql_fetch_row($rs);
if($row[0] > 0)
{
	file_put_contents('./Piao/a'.uniqid(), '');
	mysql_query('UPDATE a SET id=id-1');
}