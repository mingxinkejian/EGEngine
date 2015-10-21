<?php

namespace Core\DI;

use Exception\EGException;
class EGInstance {
	/**
	 * @var string the component ID, class name, interface name or alias name
	 */
	public $id;
	
	
	/**
	 * Constructor.
	 * @param string $id the component ID
	 */
	protected function __construct($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Creates a new Instance object.
	 * @param string $id the component ID
	 * @return Instance the new Instance object.
	 */
	public static function of($id)
	{
		return new static($id);
	}
	
	/**
	 * Resolves the specified reference into the actual object and makes sure it is of the specified type.
	 *
	 * The reference may be specified as a string or an Instance object. If the former,
	 * it will be treated as a component ID, a class/interface name or an alias, depending on the container type.
	 *
	 * If you do not specify a container, the method will first try `Yii::$app` followed by `Yii::$container`.
	 *
	 * For example,
	 *
	 * ```php
	 * use yii\db\Connection;
	 *
	 * // returns Yii::$app->db
	 * $db = Instance::ensure('db', Connection::className());
	 * // or
	 * $instance = Instance::of('db');
	 * $db = Instance::ensure($instance, Connection::className());
	 * ```
	 *
	 * @param object|string|static $reference an object or a reference to the desired object.
	 * You may specify a reference in terms of a component ID or an Instance object.
	 * @param string $type the class/interface name to be checked. If null, type check will not be performed.
	 * @param ServiceLocator|Container $container the container. This will be passed to [[get()]].
	 * @return object the object referenced by the Instance, or `$reference` itself if it is an object.
	 * @throws InvalidConfigException if the reference is invalid
	 */
	public static function ensure($reference, $type = null, $container = null)
	{
		if ($reference instanceof $type) {
			return $reference;
		} elseif (empty($reference)) {
			throw new EGException('The required component is not specified.');
		}
	
		if (is_string($reference)) {
			$reference = new static($reference);
		}
	
		if ($reference instanceof self) {
			$component = $reference->get($container);
			if ($component instanceof $type || $type === null) {
				return $component;
			} else {
				throw new EGException('"' . $reference->id . '" refers to a ' . get_class($component) . " component. $type is expected.");
			}
		}
	
		$valueType = is_object($reference) ? get_class($reference) : gettype($reference);
		throw new EGException("Invalid data type: $valueType. $type is expected.");
	}
	
	/**
	 * Returns the actual object referenced by this Instance object.
	 * @param ServiceLocator|Container $container the container used to locate the referenced object.
	 * If null, the method will first try `Yii::$app` then `Yii::$container`.
	 * @return object the actual object referenced by this Instance object.
	 */
	public function get($container = null)
	{
		if ($container) {
			return $container->get($this->id);
		}
		if (EGContainer::$app && EGContainer::$app->has($this->id)) {
			return EGContainer::$app->get($this->id);
		} else {
			return EGContainer::$instance->get($this->id);
		}
	}
}