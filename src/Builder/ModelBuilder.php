<?php

declare(strict_types=1);

namespace Randock\Model\Builder;

use Symfony\Component\Form\FormInterface;

class ModelBuilder
{
    /**
     * @return \Closure
     */
    public static function build()
    {
        return function (FormInterface $form) {
            // get data class name
            $className = $form->getConfig()->getDataClass();

            // get properties
            $reflectionClass = new \ReflectionClass($className);
            $constructorParameters = $reflectionClass->getConstructor()->getParameters();
            $parameters = [];

            /** @var \ReflectionParameter $param */
            foreach ($constructorParameters as $param) {
                if ($param->getType()->isBuiltin()) {
                    // grab data from form
                    $data = $form->get($param->getName())->getData();

                    // if null is not allowed or data has been set, then
                    // cast it to the right type
                    if ($data !== null || !$param->allowsNull()) {
                        settype($data, (string) $param->getType());
                    }

                    // store for construct
                    $parameters[$param->getName()] = $data;
                } else {
                    // check if the object/var was set via config
                    if ($form->getConfig()->hasOption($param->getName())) {
                        $object = $form->getConfig()->getOption($param->getName());
                        $parameters[$param->getName()] = $object;
                    } elseif ($param->isDefaultValueAvailable()) {
                        $parameters[$param->getName()] = $param->getDefaultValue();
                    } else {
                        // check for class map for custom types in config of FormType
                        if (!$form->getConfig()->hasOption('classes')) {
                            throw new \Exception(sprintf('Could not map %s field of type %s to constructor.', $param->getName(), $param->getType()));
                        }

                        $classes = $form->getConfig()->getOption('classes');

                        // check if the class is configured
                        if (!isset($classes[$param->getClass()->getName()])) {
                            throw new \Exception(sprintf('Could not map %s field of type %s to constructor.', $param->getName(), $param->getType()));
                        }

                        // create an empty mock
                        $class = $classes[$param->getClass()->getName()];
                        $reflection = new \ReflectionClass($class);

                        // has constructor arguments?
                        if ($reflection->getConstructor()->getNumberOfRequiredParameters() === 0) {
                            $parameters[$param->getName()] = $reflection->newInstance();
                        } else {
                            $parameters[$param->getName()] = $reflection->newInstanceWithoutConstructor();
                        }
                    }
                }
            }

            return $reflectionClass->newInstanceArgs($parameters);
        };
    }
}
