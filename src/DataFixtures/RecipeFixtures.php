<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $categories = ['Entrée', 'Plat principal', 'Dessert'];
        foreach ($categories as $category) {
            $c = (new Category())
                ->setName($category)
                ->setSlug($this->slugger->slug($category))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime))
            ;
            $this->addReference($category, $c);
            $manager->persist($c);
        }

        
        for ($i=1; $i<=10; $i++) {
            $recipe = new Recipe();
            $title = $faker->foodName();
            $recipe->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setContent($faker->paragraphs(10, true))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime))
                ->setDuration($faker->randomNumber(2, 60))
            ;
            $manager->persist($recipe);
        }
        
        $manager->flush();
    }
}
