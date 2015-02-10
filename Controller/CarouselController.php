<?php


namespace Outlandish\AcadOowpBundle\Controller;


class CarouselController extends BaseController {

	public function renderCarouselAction()
    {
        $wpHelper = $this->get('outlandish_oowp.helper.wp');

        $supporting_organisations = $wpHelper->acfOption('qmul_image');
        $supporting_text = $wpHelper->acfOption('qmul_text');
        $supporting_link = $wpHelper->acfOption('qmul_link');

        $carousel_organisations = $this->parseOrganisations($wpHelper->acfOption('associated_organisations'));

		return $this->render(
                'OutlandishAcadOowpBundle:Carousel:carousel.html.twig',
                compact(
                    'supporting_organisations',
                    'supporting_text',
                    'supporting_link',
                    'carousel_organisations'
                )
		);
	}

    private function cleanUrl($url) {

        if($url == "" || $url == "http://" ) return null;

        return preg_replace('%^(?!https?://)(.*)%', 'http://$1', $url);
    }

    private function getImageWithId($id, $image_size = 'thumbnail'){
        $image = wp_get_attachment_image_src($id, $image_size);
        return $image[0];
    }

    /**
     * @param array $organisations
     * @return array
     */
    private function parseOrganisations($organisations)
    {
        return array_map(function ($org) {
            $org['url'] = $this->cleanUrl($org['url']);
            $org['logo'] = $this->getImageWithId($org['logo'], 'medium');
            return $org;
        }, $organisations);
    }
}