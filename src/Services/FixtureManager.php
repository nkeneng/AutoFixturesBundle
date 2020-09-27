<?php


namespace Steven\AutoFixturesBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Steven\AutoFixturesBundle\Annotations\FixturableFields;
use Steven\AutoFixturesBundle\Annotations\FixturesAnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FixtureManager
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
    /**
     * @var int
     */
    private $max_page;
    /**
     * @var int
     */
    private $min_page;
    /**
     * @var int
     */
    private $text;
    /**
     * @var int
     */
    private $title;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var PropertyAccessor
     */
    private $accessor;
    /**
     * @var FixtureCreator
     */
    private $creator;

    /**
     * @var array
     */
    private $entities = [];


    public function __construct(FixturablesClasses $fixturablesClasses,
                                FixturesAnnotationReader $reader,
                                $max_page,
                                $min_page,
                                $text,
                                $title,
                                EntityManagerInterface $manager,
                                FixtureCreator $creator)
    {
        $this->fixturablesClasses = $fixturablesClasses;
        $this->reader = $reader;
        $this->max_page = $max_page;
        $this->min_page = $min_page;
        $this->text = $text;
        $this->title = $title;
        $this->manager = $manager;

        $this->setFixturablesEntities();

        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->creator = $creator;
    }

    public function createEntities()
    {
        // for each class met
        foreach ($this->classes as $class) {
            if (isset($this->entities[$class]) && count($this->entities[$class]) >= 5) {
                continue;
            }
            $entity = $this->createEntity($class);
            // get all his fixturable properties
            $annotations = $this->reader->getFixturablesProperties($entity);

            $entityType = false;
            // i look if there is a field of type entity
            foreach ($annotations as $field => $annotation) {
                if ($annotation->getType() == 'entity') {
                    $entityType = true;
                    break;
                }
            }

            if ($entityType) {
                $this->handleEntityType($class);
            } else {
                $this->handleNoEntityType($class);
            }

        }
        dump($this->entities);
//        $this->manager->flush();
    }

    public function setFixture(FixturableFields $annotation): ?string
    {
        switch ($annotation->getType()) {
            case 'title':
                return $this->creator->generateTitle($this->title !== 0 ?? 0);
                break;
            case 'text':
                return $this->creator->generateText($this->text);
                break;
            case 'name':
                return $this->creator->generateName();
                break;
            case 'city':
                return $this->creator->generateCity();
                break;
            case 'country':
                return $this->creator->generateCountry();
                break;
            case 'lastname':
                return $this->creator->generateLastname();
                break;
            case 'postcode':
                return $this->creator->generatePostcode();
                break;
            case 'imageUrl':
                return $this->creator->generateImageUrl();
                break;
            case 'entity':
                return 'entity';
                break;
        }
        return null;
    }

    public function getRelatedEntity($entity, $field)
    {
        $reflection = new \ReflectionClass(get_class($entity));
        foreach ($reflection->getProperties() as $property) {
            if ($property->name == $field) {
                // if i reach the entity property i read his annotation by looking for doctrine annotations
                $annotation = $this->reader->reader->getPropertyAnnotation($property, Annotation::class);
                $type = get_class($annotation);
                $target = $annotation->targetEntity;
                $targetExist = false;

                // test code
                // to be remove after
//               if ($target == "App\Entity\Post" ){
//                   dump($this->entities);
//                   dump($target);
//
//               }
                // if the target entity already have instances

//                if (array_key_exists($target, $this->entities)) {
//                    $targetExist = true;
//                }

                switch ($type) {
                    case ManyToOne::class:
                        // create many entities and link them to one target
                        $this->manyToOne($entity, $target, $field, $targetExist);
                        break;
                    case OneToMany::class:
                        break;
                    case ManyToMany::class:
                        break;
                    case OneToOne::class:
                        break;
                }
            }
        }
    }

    /**
     * build a relation between the target and
     * the current entity
     * @param $object
     * @param $target
     * @param $field
     * @param $number
     */
    public function associateEntities($object, $target, $field, $number)
    {
        for ($i = 0; $i <= $number; $i++) {
            $this->accessor->setValue($object, $field, $target);
        }
    }


    public function createEntity(string $class)
    {
        return new $class();
    }

    public function setFixturablesEntities()
    {
        foreach ($this->fixturablesClasses->getClassNames() as $className) {
            $entity = $this->createEntity($className);
            if ($this->reader->isFixturable($entity)) {
                $this->classes[] = $className;
            }
            unset($entity);
        }
    }

    private function handleNoEntityType($entityClass)
    {
        // create many instances
        for ($i = 0; $i < 5; $i++) {

            $newEntity = $this->createEntity($entityClass);

            $annotations = $this->reader->getFixturablesProperties($newEntity);

            foreach ($annotations as $field => $annotation) {
                $value = $this->setFixture($annotation);

                $this->accessor->setValue($newEntity, $field, $value);
            }
            $this->entities[$entityClass][] = $newEntity;
            $this->manager->persist($newEntity);
        }
    }

    private function handleEntityType($entityClass)
    {
        for ($i = 0; $i < 5; $i++) {
            $entity = $this->createEntity($entityClass);
            $annotations = $this->reader->getFixturablesProperties($entity);
            foreach ($annotations as $field => $annotation) {
                if ($annotation->getType() == 'entity') {
                    $this->getRelatedEntity($entity, $field);
                } else {
                    $value = $this->setFixture($annotation);
                    $this->accessor->setValue($entity, $field, $value);
                }
            }
            $this->entities[$entityClass][] = $entity;
            $this->manager->persist($entity);
        }

    }

    private function manyToOne($entity, $targetClass, $field, $targetExist)
    {
        $entityClass = get_class($entity);
        $target = null;
        if ($targetExist) {
            $target = $this->entities[$targetClass][0];
        } else {
            // create a new instance of target if not in array
            $target = $this->createEntity($targetClass);
            $annotations = $this->reader->getFixturablesProperties($target);
            foreach ($annotations as $currentField => $annotation) {
                $value = $this->setFixture($annotation);
                $this->accessor->setValue($target, $currentField, $value);
            }
            $this->entities[$targetClass][] = $target;

            $this->manager->persist($target);
        }
        $this->accessor->setValue($entity, $field, $target);

//        for ($i = 0; $i < 6; $i++) {
//            $newEntity = $this->createEntity($entityClass);
//
//            $annotations = $this->reader->getFixturablesProperties($newEntity);
//
//            foreach ($annotations as $currentField => $annotation) {
//
//                if ($currentField == $field) {
//                } else {
//                    $value = $this->setFixture($annotation);
//                    $this->accessor->setValue($newEntity, $currentField, $value);
//                }
//            }
//            $this->entities[$entityClass] = $newEntity;
//            $this->manager->persist($newEntity);
//        }
    }
}
