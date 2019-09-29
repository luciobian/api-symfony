<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    private const POSTS = [
        [
            "id"=>1,
            "slug"=>"hello-world",
            "title"=>"Hello World"
        ],
        [
            "id"=>2,
            "slug"=>"hello-world2",
            "title"=>"Hello World2"
        ],
        [
            "id"=>3,
            "slug"=>"hello-world3",
            "title"=>"Hello World3"
        ],
    ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page":5})
     */
    public function list($page = 1, Request $request)
    {
        $limit = $request->get('limit', 10);

        return $this->json([
            'page'=>$page,
            'link'=>$limit,
            'data'=>array_map(function($item){
                return $this->generateUrl('post_by_id', ['id'=>$item['id']]);
            },self::POSTS),
        ]);
    }

    /**
     * @Route("/{id}", name="post_by_id", requirements={"id"="\d+"})
     */
    public function post($id)
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/{slug}", name="post_by_slug")
     */
    public function postBySlug($slug)
    {
        return $this->json(
           self::POSTS[ array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }

}