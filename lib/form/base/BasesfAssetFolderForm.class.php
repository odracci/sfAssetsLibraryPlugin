<?php

/**
 * sfAssetFolder form base class.
 *
 * @method sfAssetFolder getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasesfAssetFolderForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'tree_left'     => new sfWidgetFormInputText(),
      'tree_right'    => new sfWidgetFormInputText(),
      'name'          => new sfWidgetFormInputText(),
      'relative_path' => new sfWidgetFormInputText(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'sfAssetFolder', 'column' => 'id', 'required' => false)),
      'tree_left'     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'tree_right'    => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'name'          => new sfValidatorString(array('max_length' => 255)),
      'relative_path' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'    => new sfValidatorDateTime(array('required' => false)),
      'updated_at'    => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'sfAssetFolder', 'column' => array('relative_path')))
    );

    $this->widgetSchema->setNameFormat('sf_asset_folder[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfAssetFolder';
  }


}
