<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\PostType\Post;
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

	public function renderMenuAction( $maxDepth = 1, $menu = 'header' ){

        $args = $this->generateMenuArguments($maxDepth, null, $menu);

		return $this->render(
			'OutlandishAcadOowpBundle:Menu:menuItems.html.twig',
            $args
		);
	}


    /**
     * @param Post $rootPost
     * @param int $maxDepth
     * @return Response
     */
    public function renderSideMenuAction( Post $rootPost, $maxDepth = 1 ){

        $args = $this->generateMenuArguments($maxDepth, $rootPost);

        /*add dash to root post title*/
        $rootPost->post_title = '/ '.$rootPost->post_title;
        /*override homepage as parent post*/
        $args['parent_post'] = $rootPost;

        return $this->render(
            'OutlandishAcadOowpBundle:Menu:menuItems.html.twig',
            $args
        );
    }

    public function generateMenuArguments($maxDepth = 1, $rootPost = null, $menu = null)
    {
        $this->queryManager = $this->get('outlandish_oowp.query_manager');
        $this->postManager = $this->get('outlandish_oowp.post_manager');
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
            unset($queryArgs['post_parent']);
        }

        $menuArgs = array(
            'max_depth' => $maxDepth,
            'current_depth' => 1
        );


		$posts = $this->wp_menu( $posts, $menu );


        return array(
            'posts' => $posts,
            'queryArgs' => $queryArgs,
            'menuArgs' => (object)$menuArgs
        );
    }

    public function generateFooterArguments( $menu = 'footer' )
    {
        $this->queryManager = $this->get('outlandish_oowp.query_manager');
        $this->postManager = $this->get('outlandish_oowp.post_manager');

        $homePage = $this->queryManager->query(array(
            'page_id' => get_option('page_on_front')
        ))->post;

        $pages = $this->queryManager->query(array(
            'post_type' => 'page',
            'post_parent' => 0,
            'orderby' => 'menu_order',
            'order' => 'asc'
        ));

		$pages = $this->wp_menu( $pages, $menu );

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
		} elseif ( is_single() && $postType != 'page' ) {
			$class = $this->postManager->postTypeClass($postType);
			$parent_id  = $class::postTypeParentId();
			$html .= $this->breadcrumbItem( $parent_id );

			$html .= '<li>' .the_title( '', '', false ) . "</li>";
		} elseif ( $postType == 'page' ) {

			$parents = get_post_ancestors( $post->ID );
			if ( $parents ) {
				$parents = array_reverse( $parents );
				foreach ( $parents as $parent_id ) {
					$html .= $this->breadcrumbItem( $parent_id );
				}
			}
			$html .= "<li>  " . the_title( '', '', false ) . "</li>";
		}
		$html .= "</ul>";
		echo $html;

		return new Response();
	}

	private function breadcrumbItem( $id ) {
		$title = get_the_title( $id );

		return '<li><a href="' . esc_url( get_permalink( $id ) ) . '" title="Back to ' . $title . '">' . $title . '</a> </li>';
	}

	private function wp_menu( $posts, $menu = null ) {
		if ( $menu ) {
			$menu_locations = get_nav_menu_locations();
			$menu_obj       = get_term( $menu_locations[$menu], 'nav_menu' );
			if ( !is_wp_error( $menu_obj ) ) {
				$items = wp_get_nav_menu_items( $menu_obj->term_id );
				if ( is_array( $items ) && count( $items ) > 0 ) {
					$items      = wp_list_pluck( $items, 'object_id' );
					$post_items = $posts->posts;
					$new_items  = array();
					foreach ( $post_items as $item ) {
						if ( in_array( $item->ID, $items ) ) {
							$new_items[] = $item;
						}
					}
					$posts->posts = $new_items;
				}
			}
		}

		return $posts;
	}



}