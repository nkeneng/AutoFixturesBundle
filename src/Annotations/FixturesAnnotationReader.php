<?php

namespace Steven\AutoFixturesBundle\Annotations;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;

class FixturesAnnotationReader
{
    /**
     * @var Reader
     */
    public $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * tell if an entity has the annotation
     * isFixturable
     * @param $entity
     * @return bool
     * @throws ReflectionException
     */
    public function isFixturable($entity): bool
    {
        // help getting all infos on a class
        // all properties , methods , filename , and phpDoc
        $reflection = new ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($reflection, Fixturable::class) !== null;
    }

    /**
     * return the list of all fixturable fields
     * of an fixturable entity
     * @param $entity
     * @return array
     * @throws ReflectionException
     */
    public function getFixturablesProperties($entity): array
    {
        // help getting all infos on a class
        // all properties , methods , filename , and phpDoc
        $reflection = new \ReflectionClass(get_class($entity));

        if (!$this->isFixturable($entity)) {
            return [];
        }

        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            // return an object containing the annotation data of a property
            $annotation = $this->reader->getPropertyAnnotation($property, FixturableFields::class);
            if ($annotation !== null) {
                // if the field is fixturable ( has the annoation fixturableField)
                $properties[$property->getName()] = $annotation;
            }
        }
        return $properties;
    }
}
