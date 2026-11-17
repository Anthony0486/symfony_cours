<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Book;
use Faker;
use Faker\Factory;
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
            $category->setName($faker->word());
            $categories[] = $category;
            //persister objet
            $manager->persist($category);
        }

        $users = [];
       
        for ($i = 0; $i < 10; $i++) {
            //creer objet
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $users[] = $user;
            //persister objet
            $manager->persist($user);
        }
        
        for ($i = 0; $i < 60; $i++) {
            //creer objet
            $book = new Book();
            $book->setTitle($faker->realText(10));
            $book->setAuthor($faker->name());
            $book->setPublishAt($faker->dateTime());
            $book->setResume($faker->realtext(200));
            $book->addCategory($faker->randomElement($categories));
            $book->setUser($faker->randomElement($users));
            //persister objet
            $manager->persist($book);
        }

        //envoyer et executer la requête
        $manager->flush();
    }

}
