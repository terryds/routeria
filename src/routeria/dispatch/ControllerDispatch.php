<?php
namespace Routeria\Dispatch;
use Routeria\RouterInterface;

class ControllerDispatch extends AbstractDispatch
{
	private $controller;
	private $action;
	private $params;

	private $dependencies = array();

	public function __construct($controller, $action, array $params = array())
	{
		if (!is_string($controller) && !is_object($controller)) {
			throw new \InvalidArgumentException(sprintf('Controller must be a class name or an object, given: %s', gettype($controller)));
		}
		if (is_string($controller) && !class_exists($controller)) {
			throw new \LogicException(sprintf('Controller doesnot exist. Specify the namespace if it has, given: %s', $controller));
		}
		if (!is_string($action)) {
			throw new \InvalidArgumentException(sprintf('Action must be a method name, given: %s', gettype($action)));
		}
		if (!method_exists($controller, $action)) {
			throw new \BadMethodCallException(sprintf('The controller doesnot have this method: %s', $action));
		}

		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
	}

	/**
	 * Inject an object as a dependency to the controller.
	 *
	 * Only support constructor injection.
	 * @param  object $dependency The object that the controller depends on
	 */
	public function inject($dependency)
	{
		if (!is_object($dependency) && !is_string($method)) {
			throw new \InvalidArgumentException(sprintf('Dependency must be an object, and $method must be string, given $dependency: %s , and $method: %s', gettype($dependency), gettype($method)));
		}
		$this->dependencies[] = $dependency;
		return $this;
	}

	/**
	 * Inject object dependencies to the controller
	 *
	 * The parameter $dependencies is an array with object dependencies as the values.
	 * Only support constructor injection.
	 * 		
	 * @param  array $dependencies Array of objects that the controller depends on
	 */
	public function injectDependencies(array $dependencies)
	{
		foreach ($dependencies as $method => $dependency) {
			$this->inject($dependency);
			return $this;
		}
	}

	protected function _buildController()
	{
		if (is_object($this->controller))
		{
			return $this->controller;
		}
		else
		{
			if (count($this->dependencies))
			{
				$reflect = new ReflectionClass($class);
				$controller = $reflect->newInstanceArgs($this->dependencies);
			}
			else
			{
				$controller = new $this->controllerName;
			}
		}
		return $controller;
	}

	public function dispatch(RouterInterface $router)
	{
		$controller = $this->_buildController();
		call_user_func_array(array($this->controller, $this->method), $this->params);
	}
}