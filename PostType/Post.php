<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;
use Outlandish\SiteBundle\PostType\Person;

/**
 * Class Post
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Post extends BasePost implements PostInterface
{
    /**
     * Return the name of the icon for this post type
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

    /**
     * @return int
     */
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

    /**
     * @return bool
     */
    public static function isResource()
    {
        return static::$resource;
    }

    /**
     * @return bool
     */
    public static function isTheme()
    {
        return static::$theme;
    }

    /**
     * @return bool
     */
    public static function isFilter()
    {
        return static::$searchFilter;
    }

    /**
     *
     */
    public static function onRegistrationComplete()
    {
        parent::onRegistrationComplete();
        static::registerConnections();
    }

    /**
     *
     */
    public static function registerConnections()
    {
        $class = get_called_class();
        $mapping = self::$postManager->postTypeMapping();
        $connections = array_intersect_key($class::$connections, $mapping);
        foreach ($connections as $postType => $args) {
            $class::registerConnection($postType, $args);
        }
    }

    /**
     * wraps the static property $menuIcon
     * @return bool|string
     */
    public function postTypeIcon()
    {
        return static::$menuIcon;
    }

    /**
     * @return int
     */
    protected function featuredImageAttachmentId()
    {
        $id = parent::featuredImageAttachmentId();
        if (!is_numeric($id)) {
            $id = $this->getDefaultImageId();
        }

        return $id;
    }

    /**
     * @return mixed
     */
    public function featuredImageTitle()
    {
        return $this->metadata('featured_image', true) ?: $this->metadata('title', true);
    }

    /**
     * @return mixed
     */
    public function featuredImageDescription()
    {
        return $this->metadata('featured_image', true) ?: $this->metadata('description', true);
    }

    /**
     * return custom page title
     * @return string|null
     */
    public function customPageTitle()
    {
        return $this->metadata('page_title');
    }

    /**
     * @return mixed
     */
    public function sections()
    {
        return $this->metadata('sections');
    }

    /**
     * @return mixed
     */
    public function subtitle()
    {
        return $this->metadata('subtitle');
    }

    /**
     * @return mixed
     */
    public function authorName()
    {
        return $this->metadata('author_name');
    }

    /**
     * @return mixed
     */
    public function authorDesc()
    {
        return $this->metadata('author_description');
    }

    /**
     * @return array|null|\WP_User
     */
    public function author()
    {
        $authorType = $this->metadata('author_type');

        if (!$authorType) {
            return null;
        }

        switch ($authorType) {
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
                $postAuthor = 'post_author';
                $person = Person::fetchByUser($this->{$postAuthor});

                return $person ?: new \WP_User($this->{$postAuthor});
        }
    }

    /**
     *  Returns date as
     *  (1) dd/mm - dd/mm/yyyy for events if end date set
     *  (2) post date title as dd/mm/yyyy for news
     * @param string $format     format to return date string as
     * @param bool   $shortMonth (return 'Sep' instead of 'September')
     * @return int|string
     */
    public function dateString($format = "j F Y", $shortMonth = false)
    {
        $date = '';
        $postDate = "post_date";
        if ($this->postType() == Event::postType()) {
            if ($this->endDateString()) {
                $date = $shortMonth ? $this->startDateString("j M") . " - " . $this->endDateString("j M Y") : $this->startDateString("j F") . " - " . $this->endDateString("j F Y");
            } else {
                $date = $this->startDateString($format);
            }

            if (!$date) {
                $date = date($format, strtotime($this->{$postData}));
            }
        } elseif ($this->postType() == News::postType()) {
            $date = date($format, strtotime($this->{$postDate}));
        }

        return $date;
    }

    /**
     * @param bool $isChild
     * @return array
     */
    public static function childTypes($isChild = true)
    {
        $types = array();
        $self = get_called_class();
        foreach (self::$postManager->postTypeMapping() as $postType => $class) {
            if (in_array($self, class_parents($class)) && $isChild) {
                // if the class is a child of self and we are looking for children
                $types[$postType] = $class;
            } else if (!in_array($self, class_parents($class)) && !$isChild) {
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
    public function connectedTypes($types = array())
    {
        $postTypes = self::connectedPostTypes();

        return array_intersect($postTypes, $types);
    }

    /**
     * Returns posts, grouped and titled by post type, connected to current post
     * @return array
     */
    public function allConnectedPosts()
    {
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
    public function connectedDocuments($title = 'Related documents')
    {
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
    public function connectedEvents($title = 'Related events')
    {
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
    public function connectedNews($title = 'Related news')
    {
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
    public function connectedPeople($title = 'Related people')
    {
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
    public function connectedPlaces($title = 'Related places')
    {
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
    public function connectedProjects($title = 'Related projects')
    {
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
    public function connectedThemes($title = 'Related themes')
    {
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
    public function authors()
    {
        return $this->connected(Person::postType());
    }

    /**
     * @return bool
     */
    public function hasAuthors()
    {
        $authors = $this->authors();
        $postCount = 'postCount';

        return $authors->$postCount != 0;
    }

    /**
     * returns the titles of the authors as an array
     * @return array
     */
    public function authorTitles()
    {
        $postCount = 'postCount';
        $authors = $this->authors();

        if ($authors->{$postCount} < 1) {
            return array();
        }

        return array_map(function ($a) {
            return $a->title();
        }, $authors->posts);

    }

    /**
     * @return bool|string
     */
    public function authorNames()
    {
        $authors = $this->authors();
        $names = array();
        foreach ($authors as $author) {
            $names[] = $author->title();
        }

        return (count($names) > 0) ? implode(', ', $names) : false;
    }

    /**
     * @return mixed
     */
    public function themes()
    {
        $themes = array();
        foreach (self::$postManager->postTypeMapping() as $postType => $class) {
            if ($class::isTheme()) {
                $themes[] = $postType;
            }
        }

        return $this->connected($themes);
    }

    /**
     * @return bool
     */
    public function hasThemes()
    {
        $themes = $this->themes();
        $postCount = 'post_count';

        return $themes->{$postCount} != 0;
    }

    /**
     * @return array
     */
    public function themeTitles()
    {
        $themes = $this->themes();
        $postCount = 'post_count';
        if ($themes->{$postCount} < 1) {
            return array();
        }

        return array_map(function ($a) {
            return $a->title();
        }, $themes->posts);
    }

    /**
     * @return mixed
     */
    public function recentResources()
    {
        $queryArguments = array(
            'post_type' => ['news', 'event', 'document'],
            'posts_per_page' => 10
        );

        return Post::fetchAll($queryArguments);
    }

    /**
     * @param array $postTypes
     * @param bool  $sections
     * @return array
     */
    public function relatedThemes(array $postTypes, $sections = false)
    {
        $relatedThemes = array();
        $mapping = array_intersect_key(self::$postManager->postTypeMapping(), $postTypes);
        if ($sections) {
            foreach ($mapping as $postType => $class) {
                $connected = $this->connected($postType, false, array('orderby' => 'title'));
                if ($connected->{$postCount} < 1) {
                    continue;
                }
                $relatedThemes[] = array(
                    'title' => $class::friendlyName(),
                    'items' => $connected->posts
                );
            }
        } else {
            $relatedThemes = $this->connected(array_keys($mapping), false, array('orderby' => 'title'));
        }

        return $relatedThemes;
    }

}