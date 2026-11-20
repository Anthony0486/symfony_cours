<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

use App\Entity\Book;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //utiliser faker pour generer un jeu de données factice
        $faker = Faker\Factory::create('fr_FR');
        $categories = [];
        // $category->setName("Fantastique");
        for ($i = 0; $i < 20; $i++) {
            //creer objet
            $category = new Category();
            $category->setName($faker->unique()->word());
            $categories[] = $category;
            //persister objet
            $manager->persist($category);
        }


        for ($i = 0; $i < 60; $i++) {
            //creer objet
            $book = new Book();
            $book->setTitle($faker->unique()->realText(10))
                ->setAuthor($faker->name())
                ->setPublishAt($faker->dateTime())
                ->setResume($faker->realtext(200))
                ->addCategory($faker->randomElement($categories));
            //persister objet
            $manager->persist($book);
        }

        //envoyer et executer la requête
        $manager->flush();
    }

}
