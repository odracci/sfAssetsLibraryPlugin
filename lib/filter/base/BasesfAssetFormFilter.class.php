<?php

/**
 * sfAsset filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 */
abstract class BasesfAssetFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'folder_id'   => new sfWidgetFormPropelChoice(array('model' => 'sfAssetFolder', 'add_empty' => true)),
      'filename'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description' => new sfWidgetFormFilterInput(),
      'author'      => new sfWidgetFormFilterInput(),
      'copyright'   => new sfWidgetFormFilterInput(),
      'type'        => new sfWidgetFormFilterInput(),
      'filesize'    => new sfWidgetFormFilterInput(),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'folder_id'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfAssetFolder', 'column' => 'id')),
      'filename'    => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'author'      => new sfValidatorPass(array('required' => false)),
      'copyright'   => new sfValidatorPass(array('required' => false)),
      'type'        => new sfValidatorPass(array('required' => false)),
      'filesize'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('sf_asset_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfAsset';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'folder_id'   => 'ForeignKey',
      'filename'    => 'Text',
      'description' => 'Text',
      'author'      => 'Text',
      'copyright'   => 'Text',
      'type'        => 'Text',
      'filesize'    => 'Number',
      'created_at'  => 'Date',
    );
  }
}
