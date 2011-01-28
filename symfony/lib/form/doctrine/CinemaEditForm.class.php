<?php
class CinemaEditForm extends CinemaForm
{
public function configure()
  {
  	$this->useFields(array(
  		'name', 'location_id', 'address', 'phone', 'website', 'room_count', 'lat', 'lng', 'seats', 'sound', 'ticket_price',
		'is_type_film', 'is_type_digital', 'is_type_3d', 'url_key', 'filename', 'description_teaser', 'description_content',  'meta_description', 'meta_keywords',
		'publish_date', 'service_list', 'lat', 'lng', 'map_zoom'
  	));
  	
  	$this->widgetSchema['name'] = new sfWidgetFormInput();
  	$this->widgetSchema['location_id'] = new sfWidgetFormInputHidden();
  	$this->widgetSchema['location'] = new sfWidgetFormInput();
  	$this->widgetSchema['address'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['phone'] = new sfWidgetFormInput();
  	$this->widgetSchema['website'] = new sfWidgetFormInput();
	$this->widgetSchema['room_count'] = new sfWidgetFormChoice(array(
		'choices' => array_combine(range(1, 40), range(1, 40))
	));
  	$this->widgetSchema['seats'] = new sfWidgetFormInput();
  	$this->widgetSchema['sound'] = new sfWidgetFormInput();
  	$this->widgetSchema['ticket_price'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['is_type_film'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
  	$this->widgetSchema['is_type_digital'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
  	$this->widgetSchema['is_type_3d'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
  	$this->widgetSchema['filename'] = new sfWidgetFormInputFile();
  	$this->widgetSchema['description_teaser'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['description_content'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['meta_description'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['meta_keywords'] = new sfWidgetFormTextarea();
  	$this->widgetSchema['publish_date'] = new sfWidgetFormInput();
  	$this->widgetSchema['service_list'] = new sfWidgetFormDoctrineChoice(array(
  		'multiple' => true,
  		'expanded' => true, 
  		'model' => 'Service'
  	));
	$this->widgetSchema['map_zoom'] = new sfWidgetFormInputHidden();
  	
  	$this->validatorSchema['location'] = new sfValidatorString();
  	$this->validatorSchema['filename'] = new sfValidatorFile(array('required' => false));
	$this->validatorSchema['url_key'] = new sfValidatorRegex(array('pattern' => '/^[0-9a-z\-\_]+$/', 'required' => false));
	$this->validatorSchema['url_key']->setMessage('invalid', 'Caracterele admise sunt literele, cifrele, "-", "_"');
  }
  
	public function updateObject($values = null)
  {
  	$file = $this->getValue('filename');
  	if(!isset($file)){
  		return parent::updateObject($values);
  	}
    
  	/* Delete old files */
    @unlink(sfConfig::get('app_cinema_path'). '/' . $this->getObject()->getFilename());
    @unlink(sfConfig::get('app_cinema_path'). '/t-' . $this->getObject()->getFilename());
    @unlink(sfConfig::get('app_cinema_path'). '/ts-' . $this->getObject()->getFilename());
    
  	$object = parent::updateObject($values);
  	
    $filename = md5($file->getOriginalName() . time() . rand(0, 999999)).$file->getExtension($file->getOriginalExtension());
    $file->save(sfConfig::get('app_cinema_path').'/'.$filename);
    
    $object->setFilename($filename);
    $object->createFile();
  	
  	return $object;
  }
  
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['location']))
    {
      $this->setDefault('location', $this->object->getLocation()->getCity() . ', ' . $this->object->getLocation()->getRegion());
    }

  }
}