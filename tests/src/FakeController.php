<?php

namespace Routeria\TestHelper;

class FakeController
{
	public function fakeMethod($fakeID)
	{
		echo 'Hello user id: '.$fakeID;
	}

	public static function fakeStaticMethod($fakeID)
	{
		echo 'Hello user id: '.$fakeID;
	}
}