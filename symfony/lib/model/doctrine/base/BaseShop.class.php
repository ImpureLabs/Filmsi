<?php

/**
 * BaseShop
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $url
 * @property string $filename
 * @property text $description
 * @property Doctrine_Collection $Films
 * @property Doctrine_Collection $ShopFilm
 * 
 * @method string              getName()        Returns the current record's "name" value
 * @method string              getEmail()       Returns the current record's "email" value
 * @method string              getPhone()       Returns the current record's "phone" value
 * @method string              getUrl()         Returns the current record's "url" value
 * @method string              getFilename()    Returns the current record's "filename" value
 * @method text                getDescription() Returns the current record's "description" value
 * @method Doctrine_Collection getFilms()       Returns the current record's "Films" collection
 * @method Doctrine_Collection getShopFilm()    Returns the current record's "ShopFilm" collection
 * @method Shop                setName()        Sets the current record's "name" value
 * @method Shop                setEmail()       Sets the current record's "email" value
 * @method Shop                setPhone()       Sets the current record's "phone" value
 * @method Shop                setUrl()         Sets the current record's "url" value
 * @method Shop                setFilename()    Sets the current record's "filename" value
 * @method Shop                setDescription() Sets the current record's "description" value
 * @method Shop                setFilms()       Sets the current record's "Films" collection
 * @method Shop                setShopFilm()    Sets the current record's "ShopFilm" collection
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseShop extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('shop');
        $this->hasColumn('name', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('email', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('phone', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('url', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('filename', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('description', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('import_pointer', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('import_total', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('import_url', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Film as Films', array(
             'refClass' => 'ShopFilm',
             'local' => 'shop_id',
             'foreign' => 'film_id'));

        $this->hasMany('ShopFilm', array(
             'local' => 'id',
             'foreign' => 'shop_id'));
    }
}