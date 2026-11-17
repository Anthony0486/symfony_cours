<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Faker;
use Faker\Factory;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //utiliser faker pour generer un jeu de données factice
        $faker = Faker\Factory::create('fr-FR');
        //creer objet
        $category = new Category();
        // $category->setName("Fantastique");
        for ($i = 0; $i < 10; $i++) {
        $category->setName($faker->word());
        }

        //persister objet
        $manager->persist($category);
        //envoyer et executer la requête
        $manager->flush();
    }

}
