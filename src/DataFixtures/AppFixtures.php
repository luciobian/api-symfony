<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);        
    }

    public function loadBlogPosts(ObjectManager $manager)
    {

        $user = $this->getReference('user_admin');

        $blogPost = new BlogPost();
        $blogPost->setTitle("A first post!");
        $blogPost->setPublished(new \Datetime("2019-09-29 00:00:00"));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor($user);
        $blogPost->setSlug("a-first-post");
        
        $manager->persist($blogPost);
        
        $blogPost = new BlogPost();
        $blogPost->setTitle("A second post!");
        $blogPost->setPublished(new \Datetime("2019-09-29 00:00:00"));
        $blogPost->setContent("Post text!");
        $blogPost->setAuthor($user);
        $blogPost->setSlug("a-second-post");
        $manager->persist($blogPost);
        
        $manager->flush();
    }
    public function loadComments(ObjectManager $manager)
    {
        
    }
    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('lucio');
        $user->setEmail('lucio@lucio.com');
        $user->setName('Lucio');

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            "Secret123"
        ));

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();

    }
}
