<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use App\Entity\Theme;
use App\Entity\Category;
use App\Entity\Characteristic;
use App\Entity\Consumable;
use App\Entity\Information;
use App\Entity\Notice;
use App\Entity\NoticeParagraph;
use App\Entity\Question;
use App\Entity\Resource;
use App\Entity\Tip;
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
                    ->setDescription($faker->text())
                    ->setType($faker->word())
                    ->setDisplayOrder($i)
                    ->setSlug(Urlizer::urlize($tool->getName()))
                    ->setCategory($category)
                ;

                $notice = new Notice();
                $notice->setContent('<h1>Title</h1><p>' . $faker->paragraph() . '</p>');
                $information = new Information();
                $information->setContent($faker->paragraph());
                $resource = new Resource();
                $resource->setName($faker->word())
                    ->setLink('https://start.dagoma3d.com/printer/de200/notice-utilisation')
                ;
                $tool->addResource($resource);

                for ($j=0; $j < 3; $j++) { 
                    $tip = new Tip();
                    $tip->setTitle($faker->sentence())
                        ->setYoutubeLink('https://www.youtube.com/embed/5qap5aO4i9A')
                        ->setDescription($faker->paragraph())
                    ;
                    $faq = new Question();
                    $faq->setQuestion($faker->sentence() . ' ?')
                        ->setAnswer($faker->paragraph())
                    ;
                    $consumable = new Consumable();
                    $consumable->setPrice($faker->randomNumber(2))
                        ->setName($faker->word())
                        ->setCharge($faker->paragraph())
                        ->setDescription($faker->paragraph())
                    ;
                    $characteristic = new Characteristic();
                    $characteristic->setContent($faker->sentence());
                    $tool->addQuestion($faq)
                        ->addConsumable($consumable)
                        ->addTip($tip)
                        ->addCharacteristic($characteristic)
                    ;
                }

                $tool->setInformation($information) 
                    ->setNotice($notice)
                ;
                $manager->persist($tool);
            }
        }

        $manager->flush();
    }
}
