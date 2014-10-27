<?php


namespace Outlandish\AcadOowpBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class OutlandishAcadOowpBundle extends Bundle {


	function __construct() {

        if(function_exists('add_theme_support') ) {
            add_theme_support('menus');
            register_nav_menus(
                array(
                    'header' => __('Header Navigation'),
                    'footer' => __('Footer Navigation'),
                    'footer_about' => __('Footer About')
                )
            );
        }

        if(function_exists('add_image_size')) {
            add_image_size('avatar-square', '100', '100', array('center', 'center'));
            add_image_size('item-image-12', '630', '380', array('center', 'center'));
            add_image_size('item-image-6', '307', '190', array('center', 'center'));
            add_image_size('item-image-4', '198', '122', array('center', 'center'));
        }

        if(function_exists('add_action')){
            add_action(
                'admin_menu', function () {
                    remove_menu_page( 'themes.php' );
                }
            );
            add_action(
                'admin_menu', function () {
                    add_menu_page('Menus', 'Menus', 'manage_options', 'nav-menus.php', '', 'dashicons-admin-appearance', 64);
                }
            );
        }

        if(function_exists('add_shortcode')){

            add_shortcode('outlandish_video', array($this, 'getVideo'));
        }

	}

    function getVideo($atts) {
        global $post;
        $video = '';
        print 'test!';
        if ($videoURL = $post->metadata('youtube_video_url')) {
            $id = str_replace('http://vimeo.com/', '', $videoURL);
            $video = '<iframe class="video-iframe" src="//player.vimeo.com/video/'. $id .'?title=0&amp;byline=0&amp;portrait=0&amp;color=ff9933" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }
        return $video;
    }

}