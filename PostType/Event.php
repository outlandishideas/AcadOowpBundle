<?php

namespace Outlandish\AcadOowpBundle\PostType;

abstract class Event extends Resource {

    const PREVIOUS_EVENTS_PAGE_ID = 1720;

    const NOT_FOUND_MESSAGE = 'NOT_FOUND_ENTER_POSTCODE_OR_ENTER_ADDRESS_OR_AMEND_TITLE';

	public static $menu_icon = 'dashicons-location-alt';

    public static $connections = array(
        'news' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'person' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'place' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    public function postTypeIcon() {
        return self::$menu_icon;
    }

    public static function fetchFutureEvents($queryArgs = array()) {
        $defaults = array (
            'posts_per_page' => -1,
            'meta_query'=>array(
                array(
                    'key'=>'start_date',
                    'value'=> date('Y/m/d'),
                    'compare'=>'>',
                    'type'=>'DATE'
                )
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'start_date',
            'order' => 'asc'
        );

        $queryArgs = wp_parse_args($queryArgs, $defaults);
        $futureEvents = self::fetchAll($queryArgs);

        return $futureEvents;
    }

    public static function fetchPastEvents($queryArgs = array()) {
        $defaults = array (
            'posts_per_page' => 3,
            'meta_query'=>array(
                array(
                    'key'=>'end_date',
                    'value'=> date('Y/m/d'),
                    'compare'=>'<=',
                    'type'=>'DATE'
                )
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'start_date',
            'order' => 'desc'

        );

        $queryArgs = wp_parse_args($queryArgs, $defaults);
        $pastEvents = self::fetchAll($queryArgs);

        return $pastEvents;
    }

    public static function sortByMonth($events) {

        $eventsByMonth = array();
        foreach($events as $event){
            $monthYear = $event->startMonthYearString();
            $position = '';
            foreach($eventsByMonth as $key => $value)
            {
                if($monthYear == $value['month']) {
                    $position = $key;
                    break;
                }
            }
            if (!is_int($position)) {
                $eventsByMonth[] = array (
                    'month' =>  $monthYear,
                    'posts' => array($event)
                );
            } else {
                $eventsByMonth[$position]['posts'][] = $event;
            }
        }
        return $eventsByMonth;
    }

    /**
     * return the month and year as string
     * @param string $format
     * @return bool|string
     */
    public function startMonthYearString($format = "F Y"){
        return date($format, $this->startDate());
    }

    /**
     * return start date for event as string
     * @param string $format | put in date format here
     * @return bool|string
     */
    public function startDateString($format = "j F Y"){
        return $this->startDate() ? date($format, $this->startDate()) : false;
    }

    /**
     * return the end date for event as a String
     * @param string $format
     * @return bool|string
     */
    public function endDateString($format = "j F Y"){
        return $this->endDate() ? date($format, $this->endDate()) : false;
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
        return $this->metadata('end_date') ? strtotime($this->metadata('end_date')) : false;
    }

    /**
     * return the start time as a DateTime object
     * @return int
     */
    public function startTime(){
        return $this->metadata('start_time_');

    }

    /**return address
     * @return array|string
     */
    public function address()  {
        $address = $this->metadata('event_address', true);
        if (!$address) $address = $this->postcode();
        return $address;
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