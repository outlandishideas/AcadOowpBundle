<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Page extends Post {

    const CONTACT_PAGE_ID = 102;

    //connected to document, news
    public static function onRegistrationComplete() {
        self::registerConnection(Document::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(News::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
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
                'image' => array(
                    'url' => null,
                )
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
        return $contactPeople;
    }

    public function imageUrlOnPage($id, $image_size = 'thumbnail'){
        $image = wp_get_attachment_image_src($id, $image_size);
        return $image[0];
    }

}