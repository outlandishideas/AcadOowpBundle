<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 02/02/2015
 * Time: 23:31
 */

namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\OowpBundle\Helper\WordpressHelper;

/**
 * Class HeaderController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class HeaderController extends BaseController
{
    /**
     * @return mixed
     */
    public function renderAction()
    {
        /** @var WordpressHelper $wpHelper */
        $wpHelper = $this->get('outlandish_oowp.helper.wp');

        return $this->render(
            'OutlandishAcadOowpBundle:Header:header.html.twig',
            [
                'headerImage' => $wpHelper->acfOption('header_image'),
                'headerText' => $wpHelper->acfOption('header_text')
            ]
        );
    }
}