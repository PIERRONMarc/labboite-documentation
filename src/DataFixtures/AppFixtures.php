<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use App\Entity\Theme;
use App\Entity\Category;
use Gedmo\Sluggable\Util\Urlizer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');


        $themesName = ['Outils et machines', 'Logiciels'];
        $themes = array();
        foreach ($themesName as $t) {
            $theme = new Theme();
            $theme->setName($t);
            $theme->setSlug(Urlizer::urlize($theme->getName()));
            $manager->persist($theme);
            array_push($themes, $theme);
        }

        $categories = array();
        foreach ($themes as $theme) {
            for ($i=0; $i < 5; $i++) { 
                $category = new Category();
                $category->setName($faker->word())
                    ->setSlug(Urlizer::urlize($category->getName()))
                    ->setTheme($theme)
                    ->setDisplayOrder($i)
                ;
                $manager->persist($category);
                array_push($categories, $category);
            }
        }

        foreach ($categories as $category) {
            for ($i=0; $i < 5; $i++) { 
                $tool = new Tool();
                $tool->setName($faker->word())
                    ->setPictureName('https://via.placeholder.com/150')
                    ->setDescription($faker->text())
                    ->setType($faker->word())
                    ->setDisplayOrder($i)
                    ->setSlug(Urlizer::urlize($tool->getName()))
                    ->setCategory($category)
                ;
                $manager->persist($tool);
            }
        }

        $manager->flush();
    }
}
