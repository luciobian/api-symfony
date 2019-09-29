<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle("A first post!");
        $blogPost->setPublished(new \Datetime("2019-09-29 00:00:00"));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor("Lucio");
        $blogPost->setSlug("a-first-post");
        
        $manager->persist($blogPost);
        
        $blogPost = new BlogPost();
        $blogPost->setTitle("A second post!");
        $blogPost->setPublished(new \Datetime("2019-09-29 00:00:00"));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor("Lucio");
        $blogPost->setSlug("a-second-post");
        $manager->persist($blogPost);
        
        $manager->flush();
    }
}
