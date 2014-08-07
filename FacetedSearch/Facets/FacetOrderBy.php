<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetOrderBy extends Facet {

    const SORT_RELEVANCE = 'rel';
    const SORT_RELEVANCE_LABEL = 'Relevance';
    const SORT_DATE = 'date';
    const SORT_DATE_LABEL = 'Date';
    const SORT_POPULARITY = 'pop';
    const SORT_POPULARITY_LABEL = 'Popularity';

    public $defaultAll = false;
    public $exclusive = true;

    public $defaultOptions = array(
        self::SORT_RELEVANCE => self::SORT_RELEVANCE_LABEL,
        self::SORT_DATE => self::SORT_DATE_LABEL,
        self::SORT_POPULARITY => self::SORT_POPULARITY_LABEL,
    );

    function __construct($name, $section, $options = array())
    {
        parent::__construct($name, $section, $options);
        if(empty($this->options)){
            foreach($this->defaultOptions as $name => $label){
                $this->addOption($name, $label);
            }
        }
    }

    /**
     * @param array $args
     * @return array
     */
    public function generateArguments($args = array())
    {
        $args = parent::generateArguments($args);

        //foreach option that is selected insert option as post_type
        $option = array_values($this->getSelectedOptions());
        $args['orderby'] = $option[0];

        return $args;
    }

    public function setSelected(array $params)
    {
        $affected = parent::setSelected($params);
        if ($affected == 0) {
            $optionKeys = array_keys($this->options);
            $this->options[$optionKeys[0]]['selected'] = true;
            $affected++;
        }
        return $affected;
    }

    public function filterOrderBy($orderBy, &$query) {
        global $wpdb;
        if ( $query->query_vars['orderby'] == self::SORT_RELEVANCE ) {
            $order = $query->query_vars['order'];
            $terms = $query->query_vars['search_terms'];
            $order_by = array();
            $metafields = array();
            foreach ($query->query_vars['post_type'] as $postType) {
                $postClass = $theme->postClass($postType);
                if (method_exists($postClass, 'searchableMetafields')) {
                    $metafields[$postType] = $postClass::searchableMetafields();
                } else {
                    $metafields[$postType] = array();
                }
            }
            $n = ! empty( $q['exact'] ) ? '' : '%';

            $titles = $contents = $metas = array();
            foreach ($terms as $term) {
                $term = $n . like_escape( esc_sql( $term ) ) . $n;
                $title = array(
                    "($wpdb->posts.post_title LIKE '$term')"
                );
                $titles[] = '(' . implode(' OR ', $title) . ')';
                $content = array(
                    "($wpdb->posts.post_content LIKE '$term')"
                );
                $contents[] = '(' . implode(' OR ', $content) . ')';
                $meta = array();
                foreach ($metafields as $postType=>$fields) {
                    if ($fields) {
                        $metaKeys = array();
                        foreach ($fields as $field) {
                            $metaKeys[] = "meta_match.meta_key LIKE '$field%'";
                        }
                        $metaKeyClauses = implode(' OR ', $metaKeys);
                        $meta[] = "($wpdb->posts.post_type = '$postType' AND ($metaKeyClauses) AND meta_match.meta_value LIKE '$term')";
                    }
                }
                $metas[] = '(' . implode(' OR ', $meta) . ')';
            }
            $order_by[] = 'CASE WHEN  (' . implode(' OR ', $titles) . ') THEN 1 ELSE 0 END '. $order;
            $order_by[] = 'CASE WHEN  (' . implode(' OR ', $contents) . ') THEN 1 ELSE 0 END '. $order;
            $order_by[] = 'CASE WHEN  (' . implode(' OR ', $metas) . ') THEN 1 ELSE 0 END '. $order;
            $order_by[] = "{$wpdb->posts}.post_date DESC";

            $orderBy = implode(', ', $order_by);
        }
        return $orderBy;
    }

}