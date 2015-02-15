<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\OowpBundle\PostType\Post as BasePost;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class DefaultController extends BaseController
{
    /**
     * Route is specified in routing.yml because it needs to come last
     *
     * @param mixed $slugs
     * @return Response
     */
    public function defaultPostAction($slugs)
    {
        $post = $this->queryPost($slugs, 'any', true);

        return $this->render(
            'OutlandishAcadOowpBundle:Default:post.html.twig',
            ['post' => $post]
        );
    }
}