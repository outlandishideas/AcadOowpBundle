<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 26/01/2015
 * Time: 19:58
 */

namespace Outlandish\AcadOowpBundle\Helper;

use Outlandish\OowpBundle\Helper\WordpressHelper;

/**
 * Class AcfHelper
 * @package Outlandish\AcadOowpBundle\Helper
 */
class AcfHelper
{

    /**
     * @var WordpressHelper
     */
    protected $wpHelper;

    /**
     * @param WordpressHelper $wpHelper
     */
    public function __construct(WordpressHelper $wpHelper)
    {
        $this->wpHelper = $wpHelper;
    }


    /**
     * Add a new ACF group (post) to the database
     *
     * @param string      $label
     * @param null|string $name
     *
     * @return bool success or failure of adding group
     */
    public function addGroup($label, $name = null)
    {
        if (!$name) {
            //todo: convert label to name better
            $name = str_replace('', '_', $label);
        }

        $post = array(
            'ID'             => '',
            'post_name'      => 'acf_'.$name,
            'post_title'     => $label,
            'post_status'    => 'publish',
            'post_type'      => 'acf',
            'post_date'      => date('Y-m-d H:i:s'),
            'post_date_gmt'  => date('Y-m-d H:i:s')
        );

        return wp_insert_post($post);
    }

    /**
     * @param int    $id
     * @param string $label
     * @param string $name
     * @param string $type
     * @param string $instructions
     * @return array
     */
    public function addField($id, $label, $name, $type, $instructions)
    {
        $acfs = $this->getAcfs($id);
        $fieldTypes = apply_filters('acf/registered_fields', array());

        $found = false;
        foreach ($fieldTypes as $group) {
            if (array_key_exists($type, $group)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \RuntimeException('Invalid type');
        }

        wp_reset_postdata();

        $acf = $acfs[0];
        $metadata = get_post_meta($acf->ID);

        $count = 0;
        foreach ($metadata as $key => $value) {
            if (substr($key, 0, 6) == 'field_') {
                $count++;
            }
        }

        $key = 'field_' . uniqid();
        $newField = array(
            'key' => $key,
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'instructions' => $instructions,
            'required' => 0,
            'conditional_logic' => array(
                'status' => 0,
                'rules' => array(
                    array(
                        'field' => null,
                        'operator' => '=='
                    )
                ),
                'allorany' => 'all'
            ),
            'order_no' => $count
        );
        //todo: type-specific arguments
        add_post_meta($acf->ID, $key, $newField);

        return $newField;
    }

    /**
     * @param int         $id
     * @param string      $name
     * @param null|string $newName
     * @param null|string $newLabel
     */
    public function modifyField($id, $name, $newName = null, $newLabel = null)
    {

        if (!$newName) {
            throw new \RuntimeException('rename requires new name');
        }

        $acfs = $this->getAcfs($id);

        $acf = $acfs[0];
        $metadata = get_post_meta($acf->ID);
        foreach ($metadata as $key => $value) {
            if (substr($key, 0, 6) != 'field_') {
                continue;
            }
            $acfArgs = unserialize($value[0]);
            if ($acfArgs['name'] == $name) {
                $acfArgs['name'] = $newName;
                if ($newLabel) {
                    $acfArgs['label'] = $newLabel;
                }
                update_post_meta($acf->ID, $key, $acfArgs);
                break;
            }
        }

        $db = $this->wpHelper->db();
        $db->query($db->prepare("UPDATE {$db->postmeta} SET meta_key = %s WHERE meta_key = %s", $newName, $name));
        $db->query($db->prepare("UPDATE {$db->postmeta} SET meta_key = %s WHERE meta_key = %s", '_' . $newName, '_' . $name));
    }

    /**
     * @param int $id
     * @return array
     */
    protected function getAcfs($id)
    {
        $args  = array(
            'numberposts' => 1,
            'post_type'   => 'acf',
            'post_status' => array( 'draft', 'publish', 'private', 'pending', 'future', 'auto-draft', 'trash' ),
            'p' => $id
        );

        $acfs = get_posts($args);
        if (!$acfs) {
            throw new \RuntimeException('ACF group not found');
        }

        return $acfs;
    }
}