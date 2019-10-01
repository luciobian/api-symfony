<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Facker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);        
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {

        $user = $this->getReference('user_admin');

        for ($i=0; $i < 100; $i++) { 
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear());
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($user);
            $blogPost->setSlug($this->faker->slug);
            
            $this->setReference("blog_post_".$i, $blogPost);


            $manager->persist($blogPost);
        }
        
        $manager->flush();

        echo "Blogs posts created.. \n";

    }
    public function loadComments(ObjectManager $manager)
    {
        $user = $this->getReference("user_admin");
        

        for ($i=0; $i < 100; $i++) {
            for ($j=0; $j < rand(1,10); $j++) { 
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setAuthor($user);
                $comment->setPublished($this->faker->dateTimeThisYear());
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            } 
        }

        $manager->flush();

        echo "Comments created...\n";
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
