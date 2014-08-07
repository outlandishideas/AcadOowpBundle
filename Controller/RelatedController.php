<?php


namespace Outlandish\AcadOowpBundle\Controller;


class RelatedController {

	public function renderResourcesAction( $post ) {
		$types = Theme::childTypes( false );
		unset( $types['people'] );
		$types = $post->connectedTypes( $types );



		return $this->render(
					'OutlandishAcadOowpBundle:Related:footer.html.twig',
						$post
		);

	}

} 