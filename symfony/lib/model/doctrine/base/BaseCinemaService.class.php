<?php

/**
 * BaseCinemaService
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $cinema_id
 * @property integer $service_id
 * @property Cinema $Cinema
 * @property Genre $Service
 * 
 * @method integer       getCinemaId()   Returns the current record's "cinema_id" value
 * @method integer       getServiceId()  Returns the current record's "service_id" value
 * @method Cinema        getCinema()     Returns the current record's "Cinema" value
 * @method Genre         getService()    Returns the current record's "Service" value
 * @method CinemaService setCinemaId()   Sets the current record's "cinema_id" value
 * @method CinemaService setServiceId()  Sets the current record's "service_id" value
 * @method CinemaService setCinema()     Sets the current record's "Cinema" value
 * @method CinemaService setService()    Sets the current record's "Service" value
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCinemaService extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('cinema_service');
        $this->hasColumn('cinema_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('service_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));

        $this->option('symfony', array(
             'form' => false,
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
             'onDelete' => 'CASCADE'));

        $this->hasOne('Genre as Service', array(
             'local' => 'service_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}