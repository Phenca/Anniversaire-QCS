<?php

namespace App\DataFixtures;

use App\Entity\Birthday;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $first_names = ["Alice", "Bob", "Charlie", "David", "Emma", "Frank", "Grace", "Henry", "Ivy", "Jack"];
        $last_names = ["Smith", "Johnson", "Williams", "Jones", "Brown", "Davis", "Miller", "Wilson", "Moore", "Taylor"];
        for ($i = 0; $i < 50; $i++) {
            $random_date_from_timestamp = mt_rand(strtotime('1950-01-01'), strtotime('2010-12-31'));
            $birthdate = new DateTime();
            $birthdate->setTimestamp($random_date_from_timestamp);

            $random_number = array_rand($first_names);
            $birthday = new Birthday();
            $birthday->setFirstname($first_names[$random_number])
                        ->setLastname($last_names[$random_number])
                        ->setBirthdate($birthdate);
            $manager->persist($birthday);
        }
        $manager->flush();
    }
}
