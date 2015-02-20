<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 19/02/2015
 * Time: 19:14
 */

namespace Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\AcadOowpBundle\Helper\WordpressMenuHelper;
use Outlandish\AcadOowpBundle\PageSections\PageSectionsBuilder;
use Outlandish\OowpBundle\Helper\WordpressHelper;

/**
 * Class FooterComponent
 * @package Outlandish\AcadOowpBundle\TemplateComponent
 */
class FooterComponent implements TemplateComponent
{
    /**
     * @var WordpressHelper
     */
    private $wpHelper;
    /**
     * @var WordpressMenuHelper
     */
    private $wpMenuHelper;
    /**
     * @var
     */
    private $sectionBuilder;

    public function __construct(WordpressHelper $wpHelper, WordpressMenuHelper $wpMenuHelper, PageSectionsBuilder $sectionBuilder)
    {
        $this->wpHelper = $wpHelper;
        $this->wpMenuHelper = $wpMenuHelper;
        $this->sectionBuilder = $sectionBuilder;
    }

    /**
     * Gets all the arguments managed by this component and returns them as an array
     *
     * @return array
     */
    public function getArguments()
    {
        return array(
            'sections' => $this->sectionBuilder->get(),
            'socialmedia' => $this->wpHelper->acfOption('socialmedia'),
            'pages' => $this->wpMenuHelper->get('footer'),
            'organisations' => $this->wpHelper->acfOption('associated_organisations'),
            'address' => $this->wpHelper->acfOption('address'),
            'email' => $this->wpHelper->acfOption('email'),
            'phonenumber' => $this->wpHelper->acfOption('phone_number'),
            'footer_about' => $this->wpMenuHelper->get('footer_about')
        );
    }
}
