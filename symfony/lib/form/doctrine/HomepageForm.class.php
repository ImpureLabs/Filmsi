<?php

/**
 * Homepage form.
 *
 * @package    filmsi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class HomepageForm extends BaseHomepageForm
{
  public function configure()
  {
  	$this->useFields(array(
  		'background_filename'
  	));


  	$this->widgetSchema['background_filename'] = new sfWidgetFormInputFile();

  	$this->validatorSchema['background_filename'] = new sfValidatorFile(array('required' => false));
  }

	public function updateObject($values = null)
	{
		$file = $this->getValue('background_filename');
		if(!isset($file)){
			unset($this['file']);
			return parent::updateObject($values);
		}

		$object = parent::updateObject($values);

		/* Delete old background */
		$object->deleteBackground();

		$backgroundFilename = md5($file->getOriginalName() . time() . rand(0, 999999)) . $file->getExtension($file->getOriginalExtension());

		$s3 = new AmazonS3(sfConfig::get('app_aws_key'), sfConfig::get('app_aws_secret_key'));
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_film_aws_s3_folder') . '/' . $backgroundFilename, array(
			'fileUpload' => $file->getTempName(),
			'contentType' => $file->getType(),
			'meta' => array(
				'Expires'		=> 'Thu, 16 Apr 2020 05:00:00 GMT',
				'Cache-Control' => 'max-age=315360000'
			),
			'acl' => AmazonS3::ACL_PUBLIC
		));

		if (!$response->isOk()){
			echo '<pre>'; var_dump($response);
		}

		$object->setBackgroundFilename($backgroundFilename);

		return $object;
	
	}
}
