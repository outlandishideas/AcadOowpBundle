<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\PostType\Theme;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\AcadOowpBundle\PostType\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class RelatedController extends Controller{

	private function unsetByValue( &$array, $value) {
		if ( ( $key = array_search( $value, $array ) ) !== false ) {
			unset( $array[$key] );
		}
	}
	public function renderResourcesAction( $post ) {
		$types = Theme::childTypes( false );
		$this->unsetByValue( $types, 'person' );
		$this->unsetByValue( $types, 'role' );

		$connected_types = $post->connectedTypes( $types );

		$items = $post->connected( $connected_types );

		return $this->render(
			'OutlandishAcadOowpBundle:Default:items.html.twig',
			array( 'items' => $items )
		);

	}

} 