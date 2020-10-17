<?php

namespace Steven\AutoFixturesBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use ReflectionException;
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
    private $number_per_entity;
    /**
     * @var int
     */
    private $language;
    /**
     * @var int
     */
    private $number_word_text;
    /**
     * @var int
     */
    private $number_word_title;
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
                                $number_per_entity,
                                $number_word_text,
                                $number_word_title,
                                $language,
                                EntityManagerInterface $manager,
                                FixtureCreator $creator)
    {
        $this->fixturablesClasses = $fixturablesClasses;
        $this->reader = $reader;
        $this->number_per_entity = $number_per_entity;
        $this->language = $language;
        $this->number_word_text = $number_word_text;
        $this->number_word_title = $number_word_title;
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
//        dump($this->entities);
        $this->manager->flush();
    }

    public function setFixture(FixturableFields $annotation): ?string
    {
        switch ($annotation->getType()) {
            case 'title':
                return $this->creator->generateTitle($this->number_word_title);
                break;
            case 'text':
                return $this->creator->generateText($this->number_word_text);
                break;
            case 'name':
                return $this->creator->generateName();
                break;
            case 'firstname':
                return $this->creator->generateFirstname();
                break;
            case 'phonenumber':
                return $this->creator->generatePhone();
                break;
            case 'company':
                return $this->creator->generateCompany();
                break;
            case 'email':
                return $this->creator->generateEmail();
                break;
            case 'companyemail':
                return $this->creator->generateCompanyemail();
                break;
            case 'username':
                return $this->creator->generateUsername();
                break;
            case 'datetime':
                return $this->creator->generateDatetime();
                break;
            case 'creditCardType':
                return $this->creator->generateCreditcardtype();
                break;
            case 'creditCardNumber':
                return $this->creator->generateCreditcardnumber();
                break;
            case 'creditCardExpirationDateString':
                return $this->creator->generateCreditCardExpirationDateString();
                break;
            case 'slug':
                return $this->creator->generateSlug();
                break;
            case 'url':
                return $this->creator->generateUrl();
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

    public function getRelatedEntity($entity, $field, $i)
    {
        $reflection = new \ReflectionClass(get_class($entity));
        foreach ($reflection->getProperties() as $property) {
            if ($property->name == $field) {
                // if i reach the entity property i read his annotation by looking for doctrine annotations
                $annotation = $this->reader->reader->getPropertyAnnotation($property, Annotation::class);
                $type = get_class($annotation);
                $target = $annotation->targetEntity;
                $targetExist = false;

                // if the target entity already have instances
                if (array_key_exists($target, $this->entities) && $i % 5 != 0) {
                    $targetExist = true;
                }

                switch ($type) {
                    case ManyToOne::class:
                        // create many entities and link them to one target
                        $this->manyToOne($entity, $target, $field, $targetExist);
                        break;
                    case ManyToMany::class:
                        $this->manyToMany($entity, $target, $field, $targetExist);
                        break;
                    case OneToOne::class:
                        $targetExist = false;
                        if (array_key_exists($target, $this->entities) && isset($this->entities[$target][$i])) {
                            $targetExist = true;
                        }
                        $this->oneToOne($entity, $target, $field, $targetExist, $i);
                        break;
                }
            }
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
        for ($i = 0; $i < $this->number_per_entity; $i++) {

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
        for ($i = 0; $i < $this->number_per_entity; $i++) {
            $this->createEntityType($entityClass, $i);
        }
    }

    private function manyToOne($entity, $targetClass, $field, $targetExist)
    {
        if ($targetExist) {
            $target = $this->GetRandomEntity($targetClass);
        } else {
            // create a new instance of target if not in array
            $target = $this->createNewTarget($targetClass);
        }

        $this->accessor->setValue($entity, $field, $target);
    }

    private function oneToOne($entity, $targetClass, $field, bool $targetExist, int $i)
    {
        if ($targetExist) {
            $target = $this->entities[$targetClass][$targetClass][$i];
        } else {
            // create a new instance of target if not in array
            $target = $this->createNewTarget($targetClass);
        }

        $this->accessor->setValue($entity, $field, $target);
    }

    /**
     * @param $targetClass
     * @return mixed
     */
    private function GetRandomEntity($targetClass)
    {
        return $this->entities[$targetClass][array_rand($this->entities[$targetClass])];
    }

    /**
     * @param $targetClass
     * @return mixed
     * @throws ReflectionException
     */
    private function createNewTarget($targetClass)
    {
        $i = isset($this->entities[$targetClass]) ? count($this->entities[$targetClass]) : 0;
        return $this->createEntityType($targetClass, $i);
    }

    private function manyToMany($entity, $targetClass, $field, bool $targetExist)
    {
        $target = null;
        if ($targetExist) {
            for ($i = 0; $i <= 3; $i++) {
                $target[] = $this->GetRandomEntity($targetClass);
            }
        } else {
            // create a new instance of target if not in array
            for ($i = 0; $i <= 3; $i++) {
                $target[] = $this->createNewTarget($targetClass);
            }
        }
        $this->accessor->setValue($entity, $field, $target);
    }

    /**
     * @param $entityClass
     * @param int $i
     * @return mixed
     * @throws ReflectionException
     */
    private function createEntityType($entityClass, int $i)
    {
        $entity = $this->createEntity($entityClass);
        $annotations = $this->reader->getFixturablesProperties($entity);
        foreach ($annotations as $field => $annotation) {
            if ($annotation->getType() == 'entity') {
                $this->getRelatedEntity($entity, $field, $i);
            } else {
                $value = $this->setFixture($annotation);
                $this->accessor->setValue($entity, $field, $value);
            }
        }
        $this->entities[$entityClass][] = $entity;
        $this->manager->persist($entity);
        return $entity;
    }
}
