<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;

class ActorFixtures extends Fixture
{
    public const ACTORS_THE_WALKING_DEAD = [ 
        'Andrew Lincoln',
        'Norman Reedus',
        'Lauren Cohan',
        'Danai Gurira',
        'Jeffrey Dean Morgan'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach( self::ACTORS_THE_WALKING_DEAD as $i => $actorData ) { 
 
            $actor=new Actor; 
            $actor->setName($actorData);
            $this->addReference('actor_' . $i, $actor);
            $manager->persist($actor);
        }
        $manager->flush(); 
    }
}
