<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Outlandish\SiteBundle\PostType\Event;
use Outlandish\SiteBundle\PostType\Document;
use Outlandish\SiteBundle\PostType\Place;
use Outlandish\SiteBundle\PostType\Theme;

class NavigationController extends BaseController {

    public function renderFooterAction(){
        $args = $this->generateFooterArguments();
        return $this->render(
            'OutlandishAcadOowpBundle:Navigation:footer.html.twig',
            $args
        );
    }

	public function renderMenuAction( $maxDepth = 1 ){

        $args = $this->generateMenuArguments($maxDepth);

		return $this->render(
			'OutlandishAcadOowpBundle:Menu:menuItems.html.twig',
            $args
		);
	}

    public function generateMenuArguments($maxDepth = 1)
    {
        $this->queryManager = $this->get('outlandish_oowp.query_manager');
        $this->postManager = $this->get('outlandish_oowp.post_manager');
        $rootPost = null;
        $postType = 'page';

        $queryArgs = array(
            'post_type' => $postType,
            'orderby' => 'menu_order',
            'order' => 'asc'
        );
        if ($rootPost) {
            $posts = $rootPost->children($queryArgs);
        } else {
            $class = $this->postManager->postTypeClass($postType);
            $queryArgs['post_parent'] = $class::postTypeParentId();
            $posts = $this->queryManager->query($queryArgs);
        }

        $menuArgs = array(
            'max_depth' => $maxDepth,
            'current_depth' => 1
        );

        return array(
            'posts' => $posts,
            'queryArgs' => $queryArgs,
            'menuArgs' => (object)$menuArgs
        );
    }

    public function generateFooterArguments()
    {
        $this->queryManager = $this->get('outlandish_oowp.query_manager');
        $this->postManager = $this->get('outlandish_oowp.post_manager');

        $homePage = $this->queryManager->query(array(
            'page_id' => get_option('page_on_front')
        ))->post;

        $pages = $this->queryManager->query(array(
            'post_type' => 'page',
            'orderby' => 'menu_order',
            'order' => 'asc'
        ));

        $sections = $homePage->sections();
        $organisations = get_field('associated_organisations', 'options');
        $socialMedia = get_field('social_media', 'options');
        $address = get_field('address', 'options');
        $phoneNumber = get_field('phone_number', 'options');

        return array(
            'sections' => $sections,
            'socialmedia' => $socialMedia,
            'pages' => $pages->posts,
            'organisations' => $organisations,
            'address' => $address,
            'phonenumber' => $phoneNumber
        );
    }

	public function renderBreadcrumbsAction() {
		global $wp_query;
		$post = $wp_query->get_queried_object();

		$this->queryManager = $this->get('outlandish_oowp.query_manager');
		$this->postManager = $this->get('outlandish_oowp.post_manager');
		$postType = $post->post_type;

		$html =  '<ul class="bread-nav inline-list">';

		if ( $post->ID != get_option('page_on_front') ) {
			$html .= '<li><a title="Back to Home" href="' . home_url() . '">Home</a></li>';
		}

		if ( is_search() ) {
			$html .= "<li>  Search Results</li>";
		} elseif ( is_404() ) {
			$html .= "<li>  404 Not Found</li>";
		} elseif ( is_single() ) {
			$class = $this->postManager->postTypeClass($postType);
			$parent_id  = $class::postTypeParentId();
			$parent_title = get_the_title( $parent_id );
			$html .= '<li><a href="' . esc_url( get_permalink( $parent_id ) ) . '" title="Back to '. $parent_title .'">'. $parent_title.'</a> </li>';
			$html .= '<li>' .the_title( '', '', false ) . "</li>";
		} elseif ( is_page() ) {
				// todo look for parents of page
				$html .= "<li>  " . the_title( '', '', false ) . "</li>";
		}
		$html .= "</ul>";
		echo $html;

		return new Response();
	}



}