<?php
namespace Routeria\Dispatch;
use Routeria\RouterInterface;

interface DispatchInterface
{
	public function dispatch(RouterInterface $router);
	public function toClosure();
}