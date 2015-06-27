<?php

namespace Routeria\Tests;

class FakeController
{
	public function fakeMethod($fakeID)
	{
		echo 'Hello user id: '.$fakeID;
	}
}