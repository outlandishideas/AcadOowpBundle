<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\OowpBundle\PostType\Post as OowpPost;
use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Page;
use Outlandish\OowpBundle\PostType\MiscPost;
use Symfony\Component\HttpFoundation\Response;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Outlandish\SiteBundle\PostType\Event;
use Outlandish\SiteBundle\PostType\Document;
use Outlandish\SiteBundle\PostType\Place;
use Outlandish\SiteBundle\PostType\Theme;

/**
 * Class NavigationController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class NavigationController extends SearchController
{
    /**
     * @return mixed
     */
    public function renderSocialMediaAction()
    {
        $wpHelper = $this->get('outlandish_oowp.helper.wp');

        return $this->render(
            '@OutlandishAcadOowp/Partial/socialDropdown.html.twig',
            ['socialMedia' => $wpHelper->acfOption('social_media')]
        );
    }

    /**
     * @return mixed
     */
    public function renderFooterAction()
    {
        $args = $this->generateFooterArguments();
        $footerAbout = $this->wpMenu('footer_about');
        $args['footer_about'] = $footerAbout;

        return $this->render(
            'OutlandishAcadOowpBundle:Navigation:footer.html.twig',
            $args
        );
    }

    /**
     * @param int    $maxDepth
     * @param string $menu
     * @param bool   $socialMedia
     * @param bool   $searchButton
     * @return mixed
     */
    public function renderMenuAction($maxDepth = 1, $menu = 'header', $socialMedia = false, $searchButton = false)
    {

        $args = $this->generateMenuArguments($maxDepth, null, $menu);

        if ($socialMedia) {
            $args['social_media'] = get_field('social_media', 'options');
        }

        $args['search_button'] = $searchButton;
        if ($searchButton) {
            $args['search_button_text'] = get_field('search_text', 'options');
        }

        return $this->render(
            'OutlandishAcadOowpBundle:Menu:menuItems.html.twig',
            $args
        );
    }

    /**
     * @param OowpPost $currentPost
     * @return mixed
     */
    public function renderFilterPanelAction(OowpPost $currentPost)
    {
        if ($currentPost->postType() != "page") {
            $parent = $currentPost->parent();
            if (!$parent) {
                $parent = Page::fetchById(get_option('page_on_front'));
            }
            $formAction = $parent->permalink();
        } else {
            $formAction = $currentPost->permalink();
        }

        $templateData = array(
            'formAction' => $formAction,
            'facets' => array(
                array(
                    'section' => Place::friendlyNamePlural(),
                    'options' => Place::fetchAll()->posts
                ),
                array(
                    'section' => Theme::friendlyNamePlural(),
                    'options' => Theme::fetchAll()->posts
                )
            )
        );

        return $this->render(
            'OutlandishAcadOowpBundle:Search:filterPanel.html.twig',
            $templateData
        );
    }

    /**
     * @param OowpPost $rootPost
     * @param int      $maxDepth
     * @return Response
     */
    public function renderSideMenuAction(OowpPost $rootPost, $maxDepth = 1)
    {

        $args = $this->generateMenuArguments($maxDepth, $rootPost);

        /*override homepage as parent post*/
        $args['parent_post'] = $rootPost;

        return $this->render(
            'OutlandishAcadOowpBundle:Navigation:sideMenu.html.twig',
            $args
        );
    }

    /**
     * @param int  $maxDepth
     * @param null $rootPost
     * @param null $menu
     * @return array
     */
    public function generateMenuArguments($maxDepth = 1, $rootPost = null, $menu = null)
    {
        $queryManager = $this->get('outlandish_oowp.query_manager');
        $postManager = $this->get('outlandish_oowp.post_manager');
        $postType = 'page';

        $queryArgs = array(
            'post_type' => $postType,
            'orderby' => 'menu_order',
            'order' => 'asc'
        );
        if ($menu) {
            $posts = $this->wpMenu($menu);
        } else if ($rootPost) {
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

        return array(
            'posts' => $posts,
            'queryArgs' => $queryArgs,
            'menuArgs' => (object) $menuArgs
        );
    }

    /**
     * @param string $menu
     * @return array
     */
    public function generateFooterArguments($menu = 'footer')
    {
        $queryManager = $this->get('outlandish_oowp.query_manager');

        $queryArguments = ['page_id' => get_option('page_on_front')];
        $homePage = $queryManager->query($queryArguments)->post;

        $pages = $this->wpMenu($menu);

        $sections = $homePage->sections();
        $organisations = get_field('associated_organisations', 'options');
        $socialMedia = get_field('social_media', 'options');
        $address = get_field('address', 'options');
        $email = get_field('email', 'options');
        $phoneNumber = get_field('phone_number', 'options');

        return array(
            'sections' => $sections,
            'socialmedia' => $socialMedia,
            'pages' => $pages,
            'organisations' => $organisations,
            'address' => $address,
            'email' => $email,
            'phonenumber' => $phoneNumber
        );
    }

    /**
     * @param OowpPost $post
     * @return mixed
     */
    public function renderBreadcrumbsAction(OowpPost $post)
    {
        $breadcrumb = $this->get('outlandish_acadoowp.breadcrumb_helper');

        return $this->render(
            'OutlandishAcadOowpBundle:Partial:breadcrumbs.html.twig',
            [
                'breadcrumbs' => $breadcrumb->make($post)
            ]
        );
    }

    /**
     * Returns pages in a menu object as an OOWP query
     * @param null $menu
     * @return array
     */
    private function wpMenu($menu = null)
    {
        $posts = array();
        $queryManager = $this->get('outlandish_oowp.query_manager');
        $menuLocations = get_nav_menu_locations();

        if ($menu && array_key_exists($menu, $menuLocations)) {
            $menuObject = get_term($menuLocations[$menu], 'nav_menu');
            if (!is_wp_error($menuObject)) {
                $termId = 'term_id';
                $items = wp_get_nav_menu_items($menuObject->{$termId});
                if (is_array($items) && count($items) > 0) {
                    $items = array_map(function($a) {
                        $objectId = 'object_id';

                        return $a->{$objectId};
                    }, $items);

                    $args = array(
                        'post__in' => $items,
                        'orderby' => 'post__in'
                    );
                    $posts = $queryManager->query($args)->posts;
                }
            }
        }

        return $posts;
    }


    /**
     * @return mixed
     */
    public function renderTopHeaderAction()
    {
        return $this->render(
            'OutlandishAcadOowpBundle:Navigation:topHeader.html.twig',
            array(
                'header_image' => get_field('header_image', 'options'),
                'header_text' => get_field('header_text', 'options'),
                'qmul_image' => get_field('qmul_image', 'options'),
                'qmul_text' => get_field('qmul_text_header', 'options'),
                'qmul_link' => get_field('qmul_link', 'options'),
            )
        );
    }


}