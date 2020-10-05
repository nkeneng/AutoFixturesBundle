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

    public function __construct($language)
    {
        $this->faker = $faker = Factory::create($language);
    }

    public function generateTitle(int $number = 7):string
    {
        return $this->faker->sentence($number);
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

    public function generateFirstname()
    {
        return $this->faker->firstName;
    }

    public function generatePhone()
    {
        return $this->faker->phoneNumber;
    }

    public function generateCompany()
    {
        return $this->faker->company;
    }

    public function generateEmail()
    {
        return $this->faker->email;
    }

    public function generateCompanyemail()
    {
        return $this->faker->companyEmail;
    }

    public function generateUsername()
    {
        return $this->faker->userName;
    }

    public function generateDatetime()
    {
        return $this->faker->dateTime;
    }

    public function generateCreditcardtype()
    {
        return $this->faker->creditCardType;
    }

    public function generateCreditcardnumber()
    {
        return $this->faker->creditCardNumber;
    }

    public function generateCreditcardexpirationdate()
    {
        return $this->faker->creditCardExpirationDate;
    }

    public function generateSlug()
    {
        return $this->faker->slug;
    }

    public function generateUrl()
    {
        return $this->faker->url;
    }
}
