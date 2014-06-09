<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\RoutemasterBundle\Controller\BaseController;
use Outlandish\SiteBundle\PostType\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function defaultPostAction($slugs) {
		$slugBits = explode('/', trim($slugs, '/'));
		$post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);

		return array('post' => $post);
	}

	/**
	 * @Route("/", name="home")
	 * @Template("OutlandishAcadOowpBundle:Default:frontPage.html.twig")
	 */
	public function frontPageAction() {
		$post = $this->querySingle(array('page_id' => get_option('page_on_front')));
        $news = News::fetchAll();
        $sections = array(
            array(
                'title' => 'Latest News',
                'items' => $news->posts,
                'type' => 'featured'
            )
        );
		return array(
            'post' => $post,
            'sections' => $sections
        );
	}

}