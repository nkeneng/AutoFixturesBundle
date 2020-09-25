<?php


namespace Steven\AutoFixturesBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Annotation;
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
        foreach ($this->classes as $class) {
            $entity = $this->createEntity($class);
            $annotations = $this->reader->getFixturablesProperties($entity);
            foreach ($annotations as $field => $annotation) {
                $value = $this->setFixture($annotation);
                if ($value == 'entity') {
                    $this->getRelatedEntity($entity, $field);
                } else {
                    $this->accessor->setValue($entity, $field, $value);
                }
            }
        }
    }

    public function setFixture(FixturableFields $annotation): string
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
        return '';
    }

    public function getRelatedEntity($entity, $field)
    {
        $reflection = new \ReflectionClass(get_class($entity));
        foreach ($reflection->getProperties() as $property) {
            if ($property->name == $field ) {
                $annotation = $this->reader->reader->getPropertyAnnotation($property, Annotation::class);
                $type = get_class($annotation);
                dump($annotation);
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
}
