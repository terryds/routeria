<?php

namespace Routeria;

interface RouteProviderInterface
{
	public function register(RouteCollection $collection);
}