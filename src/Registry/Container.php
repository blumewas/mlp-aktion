<?php

namespace Blumewas\MlpAktion\Registry;

/**
 * Simple Dependency Injection Container
 */
class Container
{

    /**
     * Instances resolveable
     *
     * @var array<string, AbstractDependencyType>
     */
    private $instances = [];

    /**
     * Public api for adding a factory to the container.
     *
     * Factory dependencies will have the instantiation callback invoked
     * every time the dependency is requested.
     *
     * Typical Usage:
     *
     * ```
     * $container->register( MyClass::class, $container->factory( $mycallback ) );
     * ```
     *
     * @param Closure $instantiation_callback  This will be invoked when the
     *                                         dependency is required.  It will
     *                                         receive an instance of this
     *                                         container so the callback can
     *                                         retrieve dependencies from the
     *                                         container.
     *
     * @return FactoryType  An instance of the FactoryType dependency.
     */
    public function factory(\Closure $instantiation_callback)
    {
        return new FactoryType($instantiation_callback);
    }

    /**
     * Register a class in the container.
     *
     * @param string $key Class name or identifier.
     * @param mixed $value Closure that returns the class instance.
     */
    public function register($key, $value)
    {
        if (! empty($this->instances[ $key ])) {
            return;
        }

        if (! $value instanceof FactoryType) {
            $value = new SharedType($value);
        }

        $this->instances[$key] = $value;
    }

    /**
     * Resolve and return the instance of a registered class.
     *
     * @param string $key Class name or identifier.
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->instances[$key])) {
            throw new \Exception(
                sprintf(
                    'No registered service found for: %s',
                    esc_html($key)
                )
            );
        }

        return $this->instances[ $key ]->get($this);
    }
}
