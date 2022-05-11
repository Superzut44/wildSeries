<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Actor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ActorFixtures extends Fixture
{
    public const ACTORS_THE_WALKING_DEAD = [
        ["name"=>'Andrew Lincoln', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Norman Reedus', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Lauren Cohan', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Danai Gurira', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Lesley-Ann Brandt', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
    ];

    public const ACTORS_THE_BIG_BANG_THEORY = [
        ["name"=>'Jim Parsons', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Johnny Galecki', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Kaley Cuoco', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Simon Helberg', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Kunal Nayyar', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Mayim Bialik', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Melissa Rauch', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Sara Gilbert', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
        ["name"=>'Kevin Sussman', "photo"=>'the-walking-dead-61e55381c495e460188045.jpg'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::ACTORS_THE_WALKING_DEAD as $i => $actorData) {
            $actor=new Actor();
            $actor->setName($actorData['name']);
            $actor->setPhoto($actorData['photo']);
            $actor->setUpdatedAt(new DateTime('now'));
            $this->addReference('actor_the_walking_dead_' . $i, $actor);
            $manager->persist($actor);
        }
        foreach (self::ACTORS_THE_BIG_BANG_THEORY as $i => $actorData) {
            $actor=new Actor();
            $actor->setName($actorData['name']);
            $actor->setPhoto($actorData['photo']);
            $actor->setUpdatedAt(new DateTime('now'));
            $this->addReference('actor_the_big_bang_theory_' . $i, $actor);
            $manager->persist($actor);
        }
        $manager->flush();
    }
}
