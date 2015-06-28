<?php
namespace Routeria;

abstract class CustomCollection extends RouteCollection implements CustomCollectionInterface
{
	abstract public function initialize();
}