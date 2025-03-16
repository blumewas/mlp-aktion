<?php

namespace Blumewas\MlpAktion\Registry;

abstract class AbstractDependencyType
{

    /**
     * Holds a callable or value provided for this type.
     *
     * @var mixed
     */
    private $callable_or_value;

    /**
     * Constructor
     *
     * @param mixed $callable_or_value  A callable or value for the dependency
     *                                  type instance.
     */
    public function __construct($callable_or_value)
    {
        $this->callable_or_value = $callable_or_value;
    }

    /**
     * Resolver for the internal dependency value.
     *
     * @param Container $container  The Dependency Injection Container.
     *
     * @return mixed
     */
    protected function resolve_value($container)
    {
        $callback = $this->callable_or_value;
        return \is_callable($callback)
            ? $callback($container)
            : $callback;
    }

    /**
	 * Invokes and returns the value from the stored internal callback.
	 *
	 * @param Container $container  An instance of the dependency injection
	 *                              container.
	 *
	 * @return mixed
	 */
    abstract public function get($container);
}
