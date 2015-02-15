<?php


namespace Outlandish\AcadOowpBundle\Controller;

/**
 * Class CarouselController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class CarouselController extends BaseController
{

    /**
     * @return mixed
     */
    public function renderCarouselAction()
    {
        $wpHelper = $this->get('outlandish_oowp.helper.wp');

        $supportingOrganisations = $wpHelper->acfOption('qmul_image');
        $supportingText = $wpHelper->acfOption('qmul_text');
        $supportingLink = $wpHelper->acfOption('qmul_link');

        $carouselOrganisations = $this->parseOrganisations(
            $wpHelper->acfOption('associated_organisations')
        );

        return $this->render('OutlandishAcadOowpBundle:Carousel:carousel.html.twig', [
                    'supporting_organisations' => $supportingOrganisations,
                    'supporting_text' => $supportingText,
                    'supporting_link' => $supportingLink,
                    'carousel_organisations' => $carouselOrganisations
                ]
        );
    }

    /**
     * @param array $organisations
     * @return array
     */
    private function parseOrganisations(array $organisations)
    {
        return array_map(function ($org) {
            $org['url'] = $this->cleanUrl($org['url']);
            $org['logo'] = $this->getImageWithId($org['logo'], 'medium');

            return $org;
        }, $organisations);
    }

    /**
     * @param $url
     * @return mixed|null
     */
    private function cleanUrl($url)
    {
        if ($url == "" || $url == "http://" ) {
            return null;
        }

        return preg_replace('%^(?!https?://)(.*)%', 'http://$1', $url);
    }

    /**
     * @param $id
     * @param string $imageSize
     * @return mixed
     */
    private function getImageWithId($id, $imageSize = 'thumbnail')
    {
        $image = wp_get_attachment_image_src($id, $imageSize);

        return $image[0];
    }
}