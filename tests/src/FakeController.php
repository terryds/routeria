<?php

namespace Routeria\TestHelper;

class FakeController
{
	public function fakeMethod($fakeID)
	{
		echo 'Hello user id: '.$fakeID;
	}
}