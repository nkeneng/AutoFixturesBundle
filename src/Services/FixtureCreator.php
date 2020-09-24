<?php


namespace Steven\AutoFixturesBundle\Services;


use Steven\AutoFixturesBundle\Annotations\FixturesAnnotationReader;

class FixtureCreator
{
    /**
     * @var FixturablesClasses
     */
    private $fixturablesClasses;
    /**
     * @var FixturesAnnotationReader
     */
    private $reader;

    /**
     * @var array
     */
    public $classes;

    public function __construct(FixturablesClasses $fixturablesClasses, FixturesAnnotationReader $reader)
    {
        $this->fixturablesClasses = $fixturablesClasses;
        $this->reader = $reader;
    }

    public function createEntity(string $class)
    {
        return new $class();
    }

    public function createEntities()
    {
        foreach ($this->fixturablesClasses->getClassNames() as $className) {
            $entity = $this->createEntity($className);
            if ($this->reader->isFixturable($entity)) {
                $this->classes[] = $className;
            }
        }
    }
}
