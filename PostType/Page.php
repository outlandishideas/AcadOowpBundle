<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Page extends Post {
    public static function onRegistrationComplete()
    {
        parent::onRegistrationComplete();
        if(function_exists("register_field_group"))
        {
            register_field_group(array (
                'id' => 'acf_featured-image',
                'title' => 'Featured Image',
                'fields' => array (
                    array (
                        'key' => 'field_5395d706c79d8',
                        'label' => 'Featured Image',
                        'name' => 'featured_image',
                        'type' => 'image',
                        'instructions' => 'Add a featured image for this page',
                        'save_format' => 'id',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array (
                    'position' => 'side',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                    ),
                ),
                'menu_order' => 0,
            ));
        }

    }


}