<?php

/**
 * BaseCinemaPromotion
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property string $filename
 * @property text $content
 * @property integer $cinema_id
 * @property Cinema $Cinema
 * 
 * @method string          getName()      Returns the current record's "name" value
 * @method string          getFilename()  Returns the current record's "filename" value
 * @method text            getContent()   Returns the current record's "content" value
 * @method integer         getCinemaId()  Returns the current record's "cinema_id" value
 * @method Cinema          getCinema()    Returns the current record's "Cinema" value
 * @method CinemaPromotion setName()      Sets the current record's "name" value
 * @method CinemaPromotion setFilename()  Sets the current record's "filename" value
 * @method CinemaPromotion setContent()   Sets the current record's "content" value
 * @method CinemaPromotion setCinemaId()  Sets the current record's "cinema_id" value
 * @method CinemaPromotion setCinema()    Sets the current record's "Cinema" value
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCinemaPromotion extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('cinema_promotion');
        $this->hasColumn('name', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('filename', 'string', 250, array(
             'type' => 'string',
             'length' => 250,
             ));
        $this->hasColumn('content', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('cinema_id', 'integer', null, array(
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
        $this->hasOne('Cinema', array(
             'local' => 'cinema_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));
    }
}