<?php

namespace Outlandish\AcadOowpBundle\PostType;

abstract class Event extends Post {

   //connected to document, news, person, place, project, theme
    public static function onRegistrationComplete() {
        self::registerConnection(News::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));

        if(function_exists("register_field_group"))

        {
            register_field_group(array (
                'id' => 'acf_event-details',
                'title' => 'Event Details',
                'fields' => array (
                    array (
                        'key' => 'field_4f2bc2ef29212',
                        'label' => 'Start date',
                        'name' => 'start_date',
                        'type' => 'date_picker',
                        'date_format' => 'yy/mm/dd',
                        'instructions' => 'Select the start date of the event',
                    ),
                    array (
                        'key' => 'field_4f2bc2ef24763',
                        'label' => 'End date',
                        'name' => 'end_date',
                        'type' => 'date_picker',
                        'date_format' => 'yy/mm/dd',
                        'instructions' => 'Optional: Select the end date of the event. If left blank, event is assumed to last one day.',
                    ),
                    array (
                        'key' => 'field_4f2bc2ef29rd4',
                        'label' => 'Start time',
                        'name' => 'start_time_',
                        'type' => 'text',
                        'default_value' => '7pm',
                        'formatting' => 'html',
                        'instructions' => 'Enter the start time for the event. If no start time is specified, the event will be assumed to start at 7pm.',
                    ),
                    array (
                        'key' => 'field_4f2bc36824dfe',
                        'label' => 'End time',
                        'name' => 'end_time_',
                        'type' => 'text',
                        'default_value' => '11pm',
                        'formatting' => 'html',
                        'instructions' => 'Enter the end time for the event. If no end time is specified, the event will be assumed to finish at 11pm.',
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'event',
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

}