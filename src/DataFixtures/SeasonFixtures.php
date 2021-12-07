<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS = [ 
        [ "number"=>1, "year"=>2007, 
        "description"=>"Season 1",
        "seasonReference" => 'season_The Big Bang Theory'],
        [ "number"=>2, "year"=>2008, 
        "description"=>"Season 2",
        "seasonReference" => 'season_The Big Bang Theory'],
        [ "number"=>3, "year"=>2009, 
        "description"=>"Season 3",
        "seasonReference" => 'season_The Big Bang Theory'],
        [ "number"=>4, "year"=>2010, 
        "description"=>"Season 4",
        "seasonReference" => 'season_The Big Bang Theory'],
        [ "number"=>5, "year"=>2011, 
        "description"=>"Season 5",
        "seasonReference" => 'season_The Big Bang Theory'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach( self::SEASONS as $seasons ) { 
 
            $season=new Season;  
            
            $season->setNumber($seasons['number']);  
            $season->setYear($seasons['year']);  
            $season->setDescription($seasons['description']);  
            $season->setProgram($this->getReference($seasons['seasonReference'])); 
            $this->addReference('season_' . $seasons['number'], $season);
            $manager->persist($season); 
 
        } 
        $manager->flush(); 
    }

    public function getDependencies()
    {
        return [
          ProgramFixtures::class,
        ];
    }
}
