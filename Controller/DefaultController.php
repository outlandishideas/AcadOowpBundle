<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\RoutemasterBundle\Controller\BaseController;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Page;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function defaultPostAction($slugs)
    {
		$slugBits = explode('/', trim($slugs, '/'));
        /** @var Post $post */
		$post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);
        $author = $post->author();

		return array(
            'post' => $post,
            'author' => $author
        );
	}

	/**
	 * @Route("/", name="home")
	 * @Template("OutlandishAcadOowpBundle:Default:frontPage.html.twig")
	 */
	public function frontPageAction()
    {
        /** @var Page $post */
		$post = $this->querySingle(array('page_id' => get_option('page_on_front')));
        $sections = $post->sections();
        $returnArgs = array(
            'post' => $post,
            'sections' => $sections
        );
		return $returnArgs;
    }

    /**
     * @Route("/contact/", name="contact")
     * @Template("OutlandishAcadOowpBundle:Default:contactPost.html.twig")
     */
    public function contactPostAction() {
        /** @var Page $post */
        $post = $this->querySingle(array('page_id' => Page::CONTACT_PAGE_ID));

        $response['post'] = $post;
        $response['map']  = $post->contactMap();
        $response['address'] = get_field('address', 'options');
        $response['contact_people'] = $post->contactPeople();

        return $response;
    }

    /**
     * this method checks to see whether page should show search results
     * it will add a variable to the args for that page depending on what type of page it is
     *
     */
    public function addSearchPageVariable(&$args, $override = null)
    {
        if($override === null || !is_bool($override)){
            $result = $this->isSearchPage($args);
        } else {
            $result = $override;
        }
        $args['isSearchPage'] = $result;
    }

    public function isSearchPage($args)
    {
        if(array_key_exists('q', $args)){
            return true;
        } else if (!array_key_exists('sections', $args)) {
            return true;
        } else if (empty($args['sections'])){
            return true;
        } else {
            return false;
        }
    }

}