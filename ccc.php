<?php
class A
{
	public function add(integer $x, integer $y)
	{
		return $x + $y;
	}
}

$a = new A();
echo $a->add(3, 3);