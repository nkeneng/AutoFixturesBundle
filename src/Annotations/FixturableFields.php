<?php


namespace Steven\AutoFixturesBundle\Annotations;


use InvalidArgumentException;

/**
 * @Annotation
 * @Target("PROPERTY")
 * Class FixturableFields
 * @package Steven\AutoFixturesBundle\Annotations
 */
class FixturableFields
{
    const CATEGORIES = [
        'title',
        'text',
        'name',
        'firstname',
        'lastname',
        'phonenumber',
        'company',
        'email',
        'companyemail',
        'username',
        'datetime',
        'creditCardType',
        'creditCardNumber',
        'creditCardExpirationDateString',
        'slug',
        'url',
        'city',
        'country',
        'surname',
        'postcode',
        'imageUrl',
        'entity'
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $length;

    /**
     * this class is called on every request that
     * needs an entity having this annotation
     * FixturableFields constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (empty($options['type'])) {
            throw  new InvalidArgumentException("L'annotation Uploadable doit avoir un attribut type");
        }
        if (!in_array($options['type'],self::CATEGORIES)){
            $values = '';
            foreach (self::CATEGORIES as $category) {
                $values.= $category.',';
            }
            throw new InvalidArgumentException(sprintf('the type parameter muss be one of these values %s',$values));
        }
        $this->type = $options['type'];
        $this->length = $options['length'] ?? null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }


}
