<?php

/**
 * BaseArticle
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property string $content_teaser
 * @property text $content_content
 * @property string $filename
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $url_key
 * @property bool $about_stars
 * @property date $publish_date
 * @property date $expiration_date
 * @property enum $state
 * @property integer $user_id
 * @property integer $library_id
 * @property integer $photo_album_id
 * @property integer $video_album_id
 * @property PhotoAlbum $PhotoAlbum
 * @property VideoAlbum $VideoAlbum
 * @property Doctrine_Collection $Category
 * @property sfGuardUser $Author
 * @property Doctrine_Collection $Person
 * @property Doctrine_Collection $Film
 * @property Doctrine_Collection $Cinema
 * @property Doctrine_Collection $FestivalEdition
 * @property Doctrine_Collection $ArticleCategory
 * @property Doctrine_Collection $FilmArticle
 * @property Doctrine_Collection $PersonArticle
 * @property Doctrine_Collection $CinemaArticle
 * @property Doctrine_Collection $FestivalEditionArticle
 * 
 * @method string              getName()                   Returns the current record's "name" value
 * @method string              getContentTeaser()          Returns the current record's "content_teaser" value
 * @method text                getContentContent()         Returns the current record's "content_content" value
 * @method string              getFilename()               Returns the current record's "filename" value
 * @method string              getMetaDescription()        Returns the current record's "meta_description" value
 * @method string              getMetaKeywords()           Returns the current record's "meta_keywords" value
 * @method string              getUrlKey()                 Returns the current record's "url_key" value
 * @method bool                getAboutStars()             Returns the current record's "about_stars" value
 * @method date                getPublishDate()            Returns the current record's "publish_date" value
 * @method date                getExpirationDate()         Returns the current record's "expiration_date" value
 * @method enum                getState()                  Returns the current record's "state" value
 * @method integer             getUserId()                 Returns the current record's "user_id" value
 * @method integer             getLibraryId()              Returns the current record's "library_id" value
 * @method integer             getPhotoAlbumId()           Returns the current record's "photo_album_id" value
 * @method integer             getVideoAlbumId()           Returns the current record's "video_album_id" value
 * @method PhotoAlbum          getPhotoAlbum()             Returns the current record's "PhotoAlbum" value
 * @method VideoAlbum          getVideoAlbum()             Returns the current record's "VideoAlbum" value
 * @method Doctrine_Collection getCategory()               Returns the current record's "Category" collection
 * @method sfGuardUser         getAuthor()                 Returns the current record's "Author" value
 * @method Doctrine_Collection getPerson()                 Returns the current record's "Person" collection
 * @method Doctrine_Collection getFilm()                   Returns the current record's "Film" collection
 * @method Doctrine_Collection getCinema()                 Returns the current record's "Cinema" collection
 * @method Doctrine_Collection getFestivalEdition()        Returns the current record's "FestivalEdition" collection
 * @method Doctrine_Collection getArticleCategory()        Returns the current record's "ArticleCategory" collection
 * @method Doctrine_Collection getFilmArticle()            Returns the current record's "FilmArticle" collection
 * @method Doctrine_Collection getPersonArticle()          Returns the current record's "PersonArticle" collection
 * @method Doctrine_Collection getCinemaArticle()          Returns the current record's "CinemaArticle" collection
 * @method Doctrine_Collection getFestivalEditionArticle() Returns the current record's "FestivalEditionArticle" collection
 * @method Article             setName()                   Sets the current record's "name" value
 * @method Article             setContentTeaser()          Sets the current record's "content_teaser" value
 * @method Article             setContentContent()         Sets the current record's "content_content" value
 * @method Article             setFilename()               Sets the current record's "filename" value
 * @method Article             setMetaDescription()        Sets the current record's "meta_description" value
 * @method Article             setMetaKeywords()           Sets the current record's "meta_keywords" value
 * @method Article             setUrlKey()                 Sets the current record's "url_key" value
 * @method Article             setAboutStars()             Sets the current record's "about_stars" value
 * @method Article             setPublishDate()            Sets the current record's "publish_date" value
 * @method Article             setExpirationDate()         Sets the current record's "expiration_date" value
 * @method Article             setState()                  Sets the current record's "state" value
 * @method Article             setUserId()                 Sets the current record's "user_id" value
 * @method Article             setLibraryId()              Sets the current record's "library_id" value
 * @method Article             setPhotoAlbumId()           Sets the current record's "photo_album_id" value
 * @method Article             setVideoAlbumId()           Sets the current record's "video_album_id" value
 * @method Article             setPhotoAlbum()             Sets the current record's "PhotoAlbum" value
 * @method Article             setVideoAlbum()             Sets the current record's "VideoAlbum" value
 * @method Article             setCategory()               Sets the current record's "Category" collection
 * @method Article             setAuthor()                 Sets the current record's "Author" value
 * @method Article             setPerson()                 Sets the current record's "Person" collection
 * @method Article             setFilm()                   Sets the current record's "Film" collection
 * @method Article             setCinema()                 Sets the current record's "Cinema" collection
 * @method Article             setFestivalEdition()        Sets the current record's "FestivalEdition" collection
 * @method Article             setArticleCategory()        Sets the current record's "ArticleCategory" collection
 * @method Article             setFilmArticle()            Sets the current record's "FilmArticle" collection
 * @method Article             setPersonArticle()          Sets the current record's "PersonArticle" collection
 * @method Article             setCinemaArticle()          Sets the current record's "CinemaArticle" collection
 * @method Article             setFestivalEditionArticle() Sets the current record's "FestivalEditionArticle" collection
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseArticle extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('article');
        $this->hasColumn('visit_count', 'integer', null, array(
             'type' => 'integer'
             ));
        $this->hasColumn('name', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('content_teaser', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('content_content', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('filename', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('meta_description', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('meta_keywords', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('url_key', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('about_stars', 'bool', null, array(
             'type' => 'bool',
             ));
        $this->hasColumn('publish_date', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('expiration_date', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('state', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => -1,
              1 => 0,
              2 => 1,
             ),
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('library_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('photo_album_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('video_album_id', 'integer', null, array(
             'type' => 'integer',
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('PhotoAlbum', array(
             'local' => 'photo_album_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('VideoAlbum', array(
             'local' => 'video_album_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Category', array(
             'refClass' => 'ArticleCategory',
             'local' => 'article_id',
             'foreign' => 'category_id'));

        $this->hasOne('sfGuardUser as Author', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Person', array(
             'refClass' => 'PersonArticle',
             'local' => 'article_id',
             'foreign' => 'person_id'));

        $this->hasMany('Film', array(
             'refClass' => 'FilmArticle',
             'local' => 'article_id',
             'foreign' => 'film_id'));

        $this->hasMany('Cinema', array(
             'refClass' => 'CinemaArticle',
             'local' => 'article_id',
             'foreign' => 'cinema_id'));

        $this->hasMany('FestivalEdition', array(
             'refClass' => 'FestivalEditionArticle',
             'local' => 'article_id',
             'foreign' => 'festival_edition_id'));

        $this->hasMany('ArticleCategory', array(
             'local' => 'id',
             'foreign' => 'article_id'));

        $this->hasMany('FilmArticle', array(
             'local' => 'id',
             'foreign' => 'article_id'));

        $this->hasMany('PersonArticle', array(
             'local' => 'id',
             'foreign' => 'article_id'));

        $this->hasMany('CinemaArticle', array(
             'local' => 'id',
             'foreign' => 'article_id'));

        $this->hasMany('FestivalEditionArticle', array(
             'local' => 'id',
             'foreign' => 'article_id'));

        $this->hasMany('Comment', array(
             'local' => 'library_id',
             'foreign' => 'model_library_id'));

        $inlibrary0 = new inLibrary(array(
             'type_key' => 'Article',
             'has_imdb' => false,
             'has_category' => true,
             'has_photo' => true,
             'has_video' => true,
             ));
        $this->actAs($inlibrary0);

		$timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}