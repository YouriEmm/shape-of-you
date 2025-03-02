<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Outfit;
use App\Entity\ClothingItem;
use App\Entity\Wardrobe;
use App\Entity\Partner;
use App\Entity\AINotification;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();


        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $randomRole = $roles[array_rand($roles)];


        for ($i = 0; $i < 10; $i++) {
            $randomRole = $roles[array_rand($roles)];
            $user = new User();
            $user->setName($faker->name)
                ->setEmail($faker->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles([$randomRole]);

            $manager->persist($user);

            $wardrobe = new Wardrobe();
            $wardrobe->setOwner($user);
            $manager->persist($wardrobe);

            for ($j = 0; $j < 3; $j++) {
                $outfit = new Outfit();
                $outfit->setName($faker->word)
                    ->setOwner($user)
                    ->setCreatedAt($faker->dateTimeThisYear)
                    ->setPublic(false);
                $manager->persist($outfit);

                for ($k = 0; $k < 5; $k++) {
                    $clothingItem = new ClothingItem();
                    $clothingItem->setName($faker->word)
                        ->setCategory("test")
                        ->setImage($faker->imageUrl());

                    $manager->persist($clothingItem);

                    $outfit->addItem($clothingItem);
                }
            }

            for ($l = 0; $l < 5; $l++) {
                $clothingItem = new ClothingItem();
                $clothingItem->setName($faker->word)
                    ->setCategory("test")
                    ->setImage($faker->imageUrl());

                $manager->persist($clothingItem);

                $wardrobe->addItem($clothingItem);
            }
        }

            $partner1 = new Partner();
            $partner1->setName("Zara")
                     ->setWebsite("https://www.zara.com/fr/");
            $manager->persist($partner1);

            $partner2 = new Partner();
            $partner2->setName("Levi's")
                     ->setWebsite("https://www.levi.com/FR/fr_FR/");
            $manager->persist($partner1);

        for ($n = 0; $n < 5; $n++) {
            $aiNotification = new AINotification();
            $aiNotification->setMessage($faker->sentence)
                ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeThisYear->format('Y-m-d H:i:s')))
                ->setStatus($faker->randomElement(['pending', 'processed', 'failed']));
            $manager->persist($aiNotification);
        }
        
        

        $manager->flush();
    }
}
