<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Page extends Post {

    const CONTACT_PAGE_ID = 105;

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

    /**
     * return data for map on contact page
     * @return array|string
     */
    public function contactMap()
    {
        return $this->metadata('map');
    }

    /**
     * Create an array of contact people,
     * either by (1) using existing person object or (2) manually entering details
     * if (1), check if data is of type WP_Post and convert into OOWP_Post
     *
     * @return array|string
     */
    public function contactPeople()
    {
        $contactPeopleData = $this->metadata('contact_people');
        $contactPeople = array();
        foreach ($contactPeopleData as $person) {
            $contactPerson = array(
                'name' => array(
                    'prefix' => '',
                    'content' => null,
                    'suffix' => ""
                ),
                'email' => array(
                    'prefix' => 'E: ',
                    'content' => null,
                    'suffix' => ""
                ),
                'phone' => array(
                    'prefix' => 'T: ',
                    'content' => null,
                    'suffix' => ""
                ),
            );
            if ($person['contact_person_type'] == 'existing_contact_person') {
                $wpPost = $person['contact_person_existing'];
                if($wpPost instanceof \WP_Post){
                    $id = $wpPost->ID;
                    /** @var Person $personObject */
                    $personObject = Person::fetchById($id);
                    if($personObject instanceof Person){
                        $contactPerson['name']['content'] = $personObject->title();
                        $contactPerson['email']['content'] = $personObject->email();
                        $contactPerson['phone']['content'] = $personObject->phone();
                    }
                }
            } else {
                $contactPerson['name']['content'] = $person['contact_person_name'];
                $contactPerson['email']['content'] = $person['contact_person_email'];
                $contactPerson['phone']['content'] = $person['contact_person_phone'];
            }
            $contactPeople[] = $contactPerson;
        }
        return $contactPeople;
    }

}