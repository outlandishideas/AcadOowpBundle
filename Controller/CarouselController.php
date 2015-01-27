<?php


namespace Outlandish\AcadOowpBundle\Controller;


class CarouselController extends BaseController {

	public function renderCarouselAction(){

		$args = $this->getCarousel();

		return $this->render(
					'OutlandishAcadOowpBundle:Carousel:carousel.html.twig',
						$args
		);
	}

	public function getCarousel( $args = array() )
	{
        $supportingOrganisation = get_field('qmul_image', 'options');
        $supportingText = get_field('qmul_text', 'options');
        $supportingLink = get_field('qmul_link', 'options');

        $organisations = get_field('associated_organisations', 'options');
        $carouselOrganisations = array();
        if(is_array($organisations)){
            foreach ($organisations as $org) {
                $org['url'] = $this->cleanUrl($org['url']);
                $org['logo'] = $this->getImageWithId($org['logo'], 'medium');
                $carouselOrganisations[] = $org;
            }
        }

        $args['supporting_image'] = $supportingOrganisation;
        $args['supporting_text'] = $supportingText;
        $args['supporting_link'] = $supportingLink;
        $args['carousel_organisations'] = $carouselOrganisations;

        return $args;
	}

    public function cleanUrl($url) {

        if($url == "" || $url == "http://" ) return null;

        return preg_replace('%^(?!https?://)(.*)%', 'http://$1', $url);
    }

    public function getImageWithId($id, $image_size = 'thumbnail'){
        $image = wp_get_attachment_image_src($id, $image_size);
        return $image[0];
    }
}