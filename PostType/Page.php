<?php

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Page
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Page extends Post
{

    //todo: change this to the dashicon for page
    public static $menuIcon = 'dashicons-location';

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

        if (is_array($contactPeopleData)) {
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
                    'image' => array(
                        'url' => null,
                    )
                );
                if ($person['contact_person_type'] == 'existing_contact_person') {
                    $wpPost = $person['contact_person_existing'];
                    if ($wpPost instanceof \WP_Post) {
                        $id = $wpPost->ID;
                        /** @var Person $personObject */
                        $personObject = Person::fetchById($id);
                        if ($personObject instanceof Person) {
                            $contactPerson['name']['content'] = $personObject->title();
                            $contactPerson['email']['content'] = $personObject->email();
                            $contactPerson['phone']['content'] = $personObject->phone();
                            $contactPerson['image']['url'] = $personObject->featuredImageUrl('medium');
                        }
                    }
                } else {
                    $contactPerson['name']['content'] = $person['contact_person_name'];
                    $contactPerson['email']['content'] = $person['contact_person_email'];
                    $contactPerson['phone']['content'] = $person['contact_person_phone'];
                    $contactPerson['image']['url'] = $this->imageUrlOnPage($person['contact_person_image'], 'medium');
                }
                $contactPeople[] = $contactPerson;
            }
        }

        return $contactPeople;
    }

    /**
     * @param int    $id
     * @param string $imageSize
     * @return mixed
     */
    public function imageUrlOnPage($id, $imageSize = 'thumbnail')
    {
        $image = wp_get_attachment_image_src($id, $imageSize);

        return $image[0];
    }

}