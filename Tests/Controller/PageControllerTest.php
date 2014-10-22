<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 20/10/2014
 * Time: 21:20
 */

namespace Outlandish\AcadOowpBundle\Tests\Controller;

global $_SERVER;

$_SERVER['HTTP_HOST'] = "127.0.0.1";
$_SERVER['DOCUMENT_ROOT'] = "C:/xampp/htdocs";

require_once __DIR__ . '/../../../../../../../web/wp-config.test.php';
require_once __DIR__ . '/../../../../../../../web/wp-settings.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function test_contact_us_action()
    {
        $client = static::createClient(array(), array(
            "HTTP_HOST" => "127.0.0.1"
        ));

        $_SERVER['REQUEST_URI'] = "/scaca/web/about-isci/contact-us/";
        $crawler = $client->request(
            'GET',
            '/scaca/web/about-isci/contact-us/');

        $this->assertEquals(1, $crawler->filter('h1')->count(), "Too many page titles (h1)");
        $this->assertEquals(1, $crawler->filter('.content-sidebar')->count(), "Not Enough Sidebars or too many");
        $sideMenu = $crawler->filter('.side-menu');
        $this->assertLessThanOrEqual(1, $sideMenu->count(), "Too many side menus");
        if($sideMenu->count() == 1){
            $li = $sideMenu->filter('li.current_page_item');
            $this->assertEquals(1, $li->count(), "No entry in side menu for current page");
            $this->assertEquals($client->getRequest()->getUri(), $li->filter('a')->attr('href'), "Current Page link does not match current page");
        }
        $this->assertLessThanOrEqual(1, $crawler->filter('.google-map')->count());

    }
}
 