<?php

namespace Steven\AutoFixturesBundle\Services;

use Faker\Factory;
use Faker\Generator;

class FixtureCreator
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var Generator
     */
    private $generator;

    public function __construct()
    {
        $this->faker = $faker = Factory::create('en_US');
    }

    public function generateTitle(int $number = 5):string
    {
        $sentence = $this->faker->sentence($number);
        return substr($sentence, 0, strlen($sentence) - 1);
    }

    public function generateText(int $number):string
    {
      return  $this->faker->realText($maxNbChars = $number, $indexSize = 2);
    }

    public function generateName():string
    {
        return $this->faker->name;
    }

    public function generateCity()
    {
        return $this->faker->city;
    }

    public function generateCountry()
    {
        return $this->faker->country;
    }

    public function generateLastname()
    {
        return $this->faker->lastName;
    }

    public function generatePostcode()
    {
        return $this->faker->postcode;
    }

    public function generateImageUrl()
    {
        //@TODO get the width , heigth, category from user
        return $this->faker->imageUrl(640, 480, 'cats', true, 'Faker');
    }
}
