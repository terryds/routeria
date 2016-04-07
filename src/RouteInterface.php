<?php

namespace Routeria;

interface RouteInterface
{
	public function convert($function);
	public function getInfo();
	public function getConverters();
	public function run(array $params);
}