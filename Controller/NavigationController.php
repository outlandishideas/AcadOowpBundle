<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;

class NavigationController extends BaseController {

    public function renderFooterAction(){
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
        return $this->render(
            'OutlandishAcadOowpBundle:Navigation:footer.html.twig',
            array(
                'sections' => $sections,
                'socialmedia' => $socialMedia,
                'pages' => $pages->posts,
                'organisations' => $organisations,
                'address' => $address,
                'phonenumber' => $phoneNumber
            )
        );
    }

	public function renderMenuAction( $maxDepth = 1 ){

		//$menu = $this->get('outlandish_oowp.helper.menu');
		//return $this->render( $menu->render() );

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

		return $this->render(
			'OutlandishAcadOowpBundle:Menu:menuItems.html.twig',
			array(
				'posts' => $posts,
				'queryArgs' => $queryArgs,
				'menuArgs' => (object)$menuArgs
			)
		);

	}
	
}