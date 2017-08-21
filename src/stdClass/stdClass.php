<?php

declare(strict_types=1);

namespace Randock\Model\stdClass;

/**
 * Class stdClass.
 */
class stdClass extends \stdClass
{
    /**
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        $property = preg_replace('/^set/', '', $method);
        $this->$property = $arguments[0];
    }

    /**
     * @param \stdClass $obj1
     * @param array     $array
     *
     * @return object
     */
    public static function merge(\stdClass $obj1, array $array = [])
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $obj1->$key = self::merge($obj1->$key, $value);
            } else {
                $obj1->$key = $value;
            }
        }

        return $obj1;
    }
}
