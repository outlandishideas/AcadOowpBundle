<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Person extends Post {

    //connected to document, event, news, place, project, role, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        if(function_exists("register_field_group"))
        {
            register_field_group(array (
                'id' => 'acf_name-details',
                'title' => 'Name Details',
                'fields' => array (
                    array (
                        'key' => 'field_53342e4aac032',
                        'label' => 'Title',
                        'name' => 'honorific-prefix',
                        'type' => 'select',
                        'choices' => array (
                            'Ms' => 'Ms',
                            'Miss' => 'Miss',
                            'Mrs' => 'Mrs',
                            'Mr' => 'Mr',
                            'Dr' => 'Dr',
                        ),
                        'default_value' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                    ),
                    array (
                        'key' => 'field_53342ec67354f',
                        'label' => 'Given Name',
                        'name' => 'given-name',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53342ee073550',
                        'label' => 'Additional Name',
                        'name' => 'additional-name',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53342f04a2494',
                        'label' => 'Family Name',
                        'name' => 'family-name',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53342f1aa2495',
                        'label' => 'Suffix',
                        'name' => 'honorific-suffix',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'person',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array (
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                    ),
                ),
                'menu_order' => 0,
            ));
        }
    }

    public static function friendlyNamePlural(){
        return "People";
    }



} 