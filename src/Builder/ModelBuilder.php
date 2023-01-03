<?php

declare(strict_types=1);

namespace Randock\Model\Builder;

use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionIntersectionType;
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
                $errors = [];
                $data = null;
                if ($form->getConfig()->hasOption($param->getName())
                    && $form->getConfig()->getOption($param->getName())
                       !== null) {
                    $data = $form->getConfig()->getOption($param->getName());
                }
                /** @var ReflectionNamedType[] $types */
                $types = iterator_to_array(self::getReflectionNamedTypes($param->getType()));
                foreach ($types as $type) {

                    // build in type? cast and/or ger from form
                    if ($type->isBuiltin()) {

                        // grab data from form
                        if ($form->has($param->getName())) {
                            $data = $form->get($param->getName())->getData();
                        }

                        // if null is not allowed or data has been set, then
                        // cast it to the right type
                        if ($data !== null || !$type->allowsNull()) {
                            settype($data, $type->getName());
                        }

                        // store for construct
                        $parameters[$param->getName()] = $data;
                        break;
                    } else {
                        // check if the object/var was set via config
                        if ($data !== null) {
                            $parameters[$param->getName()] = $data;
                        } elseif ($param->isDefaultValueAvailable()) {
                            $parameters[$param->getName()] = $param->getDefaultValue();
                        } else {
                            // check for class map for custom types in config of FormType
                            if (!$form->getConfig()->hasOption('classes')) {
                                $errors[] = sprintf(
                                    'Could not map %s field of type %s to constructor.',
                                    $param->getName(),
                                    $type->getName()
                                );
                                continue;
                            }

                            $classes = $form->getConfig()->getOption('classes');

                            // check if the class is configured
                            if (!isset($classes[$type->getName()])) {
                                $errors[] = sprintf(
                                    'Could not map %s field of type %s to constructor.',
                                    $param->getName(),
                                    $type->getName()
                                );
                                continue;
                            }

                            // create an empty mock
                            $class = $classes[$type->getName()];
                            $reflection = new \ReflectionClass($class);

                            // has constructor arguments?
                            if ($reflection->getConstructor()->getNumberOfRequiredParameters() === 0) {
                                $parameters[$param->getName()] = $reflection->newInstance();
                            } else {
                                $parameters[$param->getName()] = $reflection->newInstanceWithoutConstructor();
                            }
                        }
                        break;
                    }
                }
                if (!empty($errors) && !isset($parameters[$param->getName()])) {
                    throw new \Exception(implode("\n", $errors));
                }
            }

            return $reflectionClass->newInstanceArgs($parameters);
        };
    }

    /**
     * @param \ReflectionType $type
     *
     * @return \Generator<ReflectionNamedType>
     */
    private static function getReflectionNamedTypes(\ReflectionType $type): \Generator
    {
        if ($type instanceof ReflectionNamedType) {
            yield $type;
        } else {
            $types = $type->getTypes();
            foreach ($types as $item) {
                if ($item instanceof ReflectionUnionType || $item instanceof ReflectionIntersectionType) {
                    yield from self::getReflectionNamedTypes($item);
                } else {
                    yield $item;
                }
            }
        }
    }
}
