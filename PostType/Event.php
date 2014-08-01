<?php

namespace Outlandish\AcadOowpBundle\PostType;

abstract class Event extends Post {

    const NOT_FOUND_MESSAGE = 'NOT_FOUND_ENTER_POSTCODE_OR_ENTER_ADDRESS_OR_AMEND_TITLE';

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

    /**
     * return start date for event as string
     * @param string $format | put in date format here
     * @return bool|string
     */
    public function startDateString($format = "j F Y"){
        return date($format, $this->startDate());
    }

    /**
     * return the end date for event as a String
     * @param string $format
     * @return bool|string
     */
    public function endDateString($format = "j F Y"){
        return date($format, $this->endDate());
    }

    /**
     * return the start date as a DateTime object
     * @return int
     */
    public function startDate(){
        return strtotime($this->metadata('start_date'));
    }

    /**
     * return the end date as a DateTime object
     * @return int
     */
    public function endDate(){
        return strtotime($this->metadata('end_date'));
    }

    /**
     * @return array|string
     */
    public function address()  {
        return $this->metadata('event_address', true);
    }

    /**
     * @return array|string
     */
    public function postcode()  {
        return $this->metadata('event_postcode', true);
    }

    /**
     * @return int
     */
    function latitude() {
        return $this->latitudeLongitudeNumber(0);
    }

    /**
     * @return int
     */
    function longitude() {
        return $this->latitudeLongitudeNumber(1);
    }

    /**
     * @return array|string
     */
    public function latitudeLongitude()
    {
        return $this->metadata('event_latitude_longitude') != self::NOT_FOUND_MESSAGE ? $this->metadata('event_latitude_longitude') : '';
    }

    /**
     * Get latitude(index = 0) and longitude (index = 1) for event by their index number
     * Return false if latitudeLongitude not found, or if index parameter is > 2 or < 0
     *
     * @param $index
     * @return bool
     */
    public function latitudeLongitudeNumber($index)
    {
        if(!$this->latitudeLongitude()) return false;
        $lat_lng = explode(",", $this->latitudeLongitude());
        if(count($lat_lng) > 2 || $index > 1 || $index < 0) return false;

        return $lat_lng[$index];
    }

    /**
     * Attempt to save event coordinates from
     * (1) event address acf and postcode acf
     * (2) event postcode acf
     * * If unsuccessful, insert NOT_FOUND_MESSAGE in 'event_latitude_longitude' acf    *
     *
     * return false
     * */
    public function onSave($postData) {
        if ($this->post_type == Event::postType() && ($this->latitudeLongitude() == '' || $this->latitudeLongitude() == self::NOT_FOUND_MESSAGE )){
            $location = urlencode($this->address() . $this->postcode());
            $data = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address={$location}&sensor=false"));
            if ($data->status == 'OK') {
                $lat = $data->results[0]->geometry->location->lat;
                $lng = $data->results[0]->geometry->location->lng;
                $value = $lat . "," . $lng;
            } else {
                $value = self::NOT_FOUND_MESSAGE;
            }
            update_post_meta($this->ID, "event_latitude_longitude", $value);
        }
        return false;
    }
}