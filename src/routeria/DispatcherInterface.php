<?php
namespace Routeria;
use Symfony\Component\HttpFoundation\Request;

interface DispatcherInterface
{
	public function dispatch(Request $request);
}