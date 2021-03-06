<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;
use Outlandish\SiteBundle\PostType\Person;


abstract class Post extends BasePost {

    /**
     * return the name of the icon for this post type
     * todo: change this to the post icon from dashicons
     * @var string
     */
    public static $menuIcon = 'dashicons-location';

    public static $resource = false;
    /**
     * states whether this post type is a theme. Themes are categories that are used to populate search filter
     * @var bool
     */
    public static $theme = false;
    /**
     * this overrides theme on whether post type should be added as a search filter
     * @var bool
     */
    public static $searchFilter = false;

    /**
     * id of media in db that is the default image id
     * @var int
     */
    protected $defaultImageId;

    public function getDefaultImageId()
    {
        return $this->defaultImageId;
    }

    /**
     * @return bool
     */
    public static function isSearchFilter()
    {
        return static::$searchFilter;
    }

    /**
     * this lists the connections for this post
     * array key = internal name of post type (eg. post)
     * array value = $args to be passed to registerConnection($postType, $args)
     * @var array
     */
    public static $connections = array();

    public static function isResource(){
        return static::$resource;
    }

    public static function isTheme(){
        return static::$theme;
    }

    public static function isFilter(){
        return static::$searchFilter;
    }

    public static function onRegistrationComplete() {
        parent::onRegistrationComplete();
        static::registerConnections();
    }

    public static function registerConnections()
    {
        $class = get_called_class();
        $mapping = self::$postManager->postTypeMapping();
        $connections = array_intersect_key($class::$connections, $mapping);
        foreach($connections as $postType => $args) {
            $class::registerConnection($postType, $args);
        }
    }

    /**
     * wraps the static property $menuIcon
     * @return bool|string
     */
    public function postTypeIcon() {
        return static::$menuIcon;
    }

    protected function featuredImageAttachmentId()
    {
        $id = parent::featuredImageAttachmentId();
        if(!is_numeric($id)){
            $id = $this->getDefaultImageId();
        }
        return $id;
    }

    public function featuredImageTitle() {
        return $this->metadata('featured_image', true) ?: $this->metadata('title', true);
    }

    public function featuredImageDescription() {
        return $this->metadata('featured_image', true) ?: $this->metadata('description', true);
    }

    /**
     * return custom page title
     * @return string|null
     */
    public function customPageTitle() {
        return $this->metadata('page_title');
    }

    public function sections()
    {
        return $this->metadata('sections');
    }

    public function subtitle()
    {
        return $this->metadata('subtitle');
    }

    public function authorName()
    {
        return $this->metadata('author_name');
    }

    public function authorDesc()
    {
        return $this->metadata('author_description');
    }

    public function author()
    {
        $authorType = $this->metadata('author_type');

        if(!$authorType) return null;

        switch($authorType){
            case 'acf':
                return array(
                    'name' => $this->authorName(),
                    'description' => $this->authorDesc()
                );
            break;
            case 'post':
                return $this->connected(Person::postType())->posts;
            break;
            default:
                $person = Person::fetchByUser($this->post_author);
                if($person) {
                    return $person;
                } else {
                    return new \WP_User( $this->post_author);
                }
        }
    }

    /**
     *  Returns date as
     *  (1) dd/mm - dd/mm/yyyy for events if end date set
     *  (2) post date title as dd/mm/yyyy for news
     * @param bool $shortMonth (return 'Sep' instead of 'September')
     * @return int|string
     */
    public function dateString($format = "j F Y", $shortMonth = false) {

        $date = '';

        if ($this->post_type == Event::postType()) {

            if ($this->endDateString()) {
                $date = $shortMonth ? $this->startDateString("j M")." - " . $this->endDateString("j M Y") : $this->startDateString("j F")." - " . $this->endDateString("j F Y");
            } else {
                $date = $this->startDateString($format);
            }
            if (!$date) {
                $date = date($format, strtotime($this->post_date));
            }

        } elseif ($this->post_type == News::postType()) {
            $date = date($format, strtotime($this->post_date));
        }
//        elseif ($this->post_type == Theme::postType() || $this->post_type == Person::postType()) {
//            $date = date($format, strtotime($this->post_modified));
//        }

        return $date;
    }

	public static function childTypes( $isChild = true ) {
		$types = array();
		$self = get_called_class();
		foreach (self::$postManager->postTypeMapping() as $postType => $class) {

			if ( in_array( $self, class_parents( $class) ) && $isChild ) {
				// if the class is a child of self and we are looking for children
				$types[$postType] = $class;
			} else if (	! in_array( $self, class_parents( $class) ) && ! $isChild) {
				// if class not child of self and we are not looking for children
				$types[$postType] = $class;
			}
		}

		return $types;
	}

    /**
     * @param array $types
     * @return array
     */
    public function connectedTypes( $types = array() ) {
		$post_types = self::connectedPostTypes();

		return array_intersect( $post_types, $types);
	}

    /**
     * Returns posts, grouped and titled by post type, connected to current post
     * @return array
     */
    public function allConnectedPosts() {
        $allConnectedPosts = array(
            $this->connectedThemes(),
            $this->connectedDocuments(),
            $this->connectedEvents(),
            $this->connectedNews(),
            $this->connectedPeople(),
            $this->connectedPlaces(),
            $this->connectedProjects()
        );
        return $allConnectedPosts;
    }


    /**
     * Returns a title and all document posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedDocuments($title = 'Related documents') {
        return array(
            'title' => $title,
            'items' => $this->connected(Document::postType()),
            'postType' => Document::postType()
        );
    }

    /**
     * Returns a title and all event posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedEvents($title = 'Related events') {
        return array(
            'title' => $title,
            'items' => $this->connected(Event::postType()),
            'postType' => Event::postType()
        );
    }

    /**
     * Returns a title and all Article posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedNews($title = 'Related news') {
        return array(
            'title' => $title,
            'items' => $this->connected(News::postType()),
            'postType' => News::postType()
        );
    }

    /**
     * Returns a title and all person posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedPeople($title = 'Related people') {
        return array(
            'title' => $title,
            'items' => $this->connected(Person::postType()),
            'postType' => Person::postType()
        );
    }

    /**
     * Returns a title and all place posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedPlaces($title = 'Related places') {
        return array(
            'title' => $title,
            'items' => $this->connected(Place::postType()),
            'postType' => Place::postType()
        );
    }

    /**
     * Returns a title and all project posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedProjects($title = 'Related projects') {
        return array(
            'title' => $title,
            'items' => $this->connected(Project::postType()),
            'postType' => Project::postType()
        );
    }

    /**
     * Returns a title and all theme posts connected to current post
     * @param string $title
     * @return array
     */
    public function connectedThemes($title = 'Related themes') {
        return array(
            'title' => $title,
            'items' => $this->connected(Theme::postType()),
            'postType' => Theme::postType()
        );
    }

    /**
     * returns the authors connected with a post
     * @return null|\Outlandish\OowpBundle\PostType\Post|\Outlandish\OowpBundle\Query\OowpQuery
     */
    public function authors() {
        return $this->connected( Person::postType() );
    }

    public function hasAuthors()
    {
        $authors = $this->authors();
        return $authors->post_count != 0;
    }

    /**
     * returns the titles of the authors as an array
     * @return array
     */
    public function authorTitles()
    {
        $authors = $this->authors();
        if($authors->post_count < 1) return array();
        return array_map(function($a){
            return $a->title();
        }, $authors->posts);

    }

    public function author_names() {
        $authors = $this->authors();
        $names = array();
        foreach( $authors as $author ) {
            $names[] = $author->title();
        }

        return ( count( $names ) > 0 ) ? implode( ', ', $names ) : false;
    }

    public function themes()
    {
        $themes = array();
        foreach(self::$postManager->postTypeMapping() as $postType => $class) {
            if($class::isTheme()) $themes[] = $postType;
        }
        return $this->connected($themes);
    }

    public function hasThemes()
    {
        $themes = $this->themes();
        return $themes->post_count != 0;
    }

    public function themeTitles()
    {
        $themes = $this->themes();
        if($themes->post_count < 1) return array();
        return array_map(function($a){
            return $a->title();
        }, $themes->posts);
    }

    public function recentResources () {
        $recentResources = Post::fetchAll(array(
            'post_type' =>
                array(
                    'news',
                    'event',
                    'document'
            ),
            'posts_per_page' => 10));
        return $recentResources;
    }

    public function socialMedia() {
        return get_field('social_media', 'options');
    }

    public function searchButtonText() {
        return get_field('search_text', 'options');
    }

    public function headerImage() {
        return get_field('header_image', 'options');
    }

    public function headerText() {
        return get_field('header_text', 'options');
    }

}