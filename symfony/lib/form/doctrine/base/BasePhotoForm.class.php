<?php

/**
 * Photo form base class.
 *
 * @method Photo getObject() Returns the current form's model object
 *
 * @package    filmsi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePhotoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'filename'    => new sfWidgetFormInputText(),
      'description' => new sfWidgetFormInputText(),
      'state'       => new sfWidgetFormChoice(array('choices' => array(-1 => -1, 0 => 0, 1 => 1))),
	  'on_home'     => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1)),
	  'is_redcarpet'=> new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1)),
      'album_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Album'), 'add_empty' => true)),
      'position'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'filename'    => new sfValidatorString(array('max_length' => 50)),
      'description' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'state'       => new sfValidatorChoice(array('choices' => array(0 => -1, 1 => 0, 2 => 1), 'required' => false)),
	  'on_home'     => new sfValidatorPass(array('required' => false)),
	  'is_redcarpet'=> new sfValidatorPass(array('required' => false)),
      'album_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Album'), 'required' => false)),
      'position'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Photo', 'column' => array('position', 'album_id')))
    );

    $this->widgetSchema->setNameFormat('photo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Photo';
  }

}
