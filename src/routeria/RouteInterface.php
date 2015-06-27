<?php

namespace Routeria;

interface RouteInterface
{
	public function convert($name, $converter);
	public function getPattern();
	public function getCallback();
	public function getHttpMethod();
	public function getConverters();
}