<?php
/**
 * User: Milos Savic
 */

require_once 'Provider.php';

class Container implements ArrayAccess {

    private $providers = array(); // providers array
    private $shared = array(); // store true for singleton
    private $params = array(); // parameters for provider

    public function __construct() {
        $reflect = new ReflectionClass($this);
        $reflect->getProperties();
    }

    /**
     * Sets the provider
     * @param string $name - provider's name
     * @param string|object|array|resource|closure - provider
     * @param boolean $singleton
     */
    public function set($name, $provider, $singleton = false) {

        if ($singleton === false || ($singleton === true && !isset($this->providers[$name]))) {
            $this->providers[$name] = $provider;
        }

        if ($singleton === true) {
            $this->shared[$name] = $singleton;
        }
    }

    /**
     * Gets the provider
     * @param string $name
     * @param array $params
     * @return string|object|array|resource
     */
    public function get($name, $params = array()) {

        if (isset($this->providers[$name])) {

            if (!empty($params))
                $this->params[$name] = $params;

            if (is_scalar($this->providers[$name]) || (is_object($this->providers[$name]) && !$this->providers[$name] instanceof Closure) || is_resource($this->providers[$name]) || is_array($this->providers[$name]))
                return $this->providers[$name];

            elseif ($this->providers[$name] instanceof Closure) {

                if (empty($params) && isset($this->params[$name]))
                    $params = $this->params[$name];

                if (isset($this->shared[$name]) && $this->shared[$name] === true) {
                    return call_user_func_array($this->providers[$name], $params);
                }
                else {
                    return call_user_func_array($this->providers[$name], $params);
                }
            }

            else
                return "Provider type isn't correct.";
        }
        return "Provider " .$name. " is not found.";
    }

    /**
     * Sets the provider via magic methods
     * @param string $name - provider name
     * @param string|object|array|resource|closure - provider
     */
    public function __set($name, $provider) {

        $this->set($name, $provider);
    }

    /**
     * Gets the provider via magic methods
     * @param string $name - provider name
     * @return string|object|array|resource
     */
    public function __get($name) {

        if (array_key_exists($name, $this->providers)) {
            return $this->get($name);
        }
        return "Provider " .$name. " doesn't exist.";
    }

    /**
     * Implements method in ArrayAccess interface to provide accessing objects as arrays
     * Assign a value (provider) to the specified offset (in our case, it's a provider name
     * @param string $name - provider name
     * @param mixed $provider - provider
     */
    public function offsetSet($name, $provider) {
        if (is_null($name)) {
            $this->providers[] = $provider;
        } else {
            $this->set($name, $provider);
        }
    }

    /**
     * Implements method in ArrayAccess interface to provide accessing objects as arrays
     * Whether an offset exists (in our case, checks a provider name)
     * @param string $name - provider name
     * @return boolean
     */
    public function offsetExists($name) {
        return isset($this->providers[$name]);
    }

    /**
     * Implements method in ArrayAccess interface to provide accessing objects as arrays
     * Unset an offset (in our case, it's a provider name)
     * @param string $name - provider name
     */
    public function offsetUnset($name) {
        unset($this->providers[$name]);
    }

    /**
     * Implements method in ArrayAccess interface to provide accessing objects as arrays
     * Offset to retrieve (in our case, gets a provider)
     * @param string $name - provider name
     * @return mixed $provider - provider
     */
    public function offsetGet($name) {
        return $this->get($name);
    }

    /**
     * Accessing providers as function using call magic method
     * it's triggered when invoking inaccessible methods in an object context
     * @param string $name - provider name
     * @param array $params - arguments
     * @return string|object|array|resource
     */
    public function __call($name, $params = array()) {

        return $this->get($name, $params);
    }

    /**
     * Registers a provider
     * @param Provider $provider
     * @return static
     */
    public function register(Provider $provider) {

        $provider->register($this);
        return $this;
    }
    
}