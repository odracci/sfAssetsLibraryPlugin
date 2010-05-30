<?php

/**
 * sfAsset form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetForm extends BasesfAssetForm
{
  public function configure()
  {
    // hide some fields
    unset($this['created_at'], $this['filesize']);

    // filename not required (since it's extracted from file)
    $this->validatorSchema['filename']->setOption('required', false);

    if ($this->getObject()->isNew())  // new asset (create)
    {
      // add hidden parent folder
      $this->widgetSchema['folder_id'] = new sfWidgetFormInputHidden();
      if (!empty($this->options['parent_id']))
      {
        $this->setDefault('folder_id', $this->options['parent_id']);
      }

      // add file input
      $this->widgetSchema['file'] = new sfWidgetFormInputFile();
      $this->validatorSchema['file'] = new sfValidatorFile();
    }
    else  // old asset (edit)
    {
      // hide other fields
      unset($this['folder_id'], $this['filename']);

      // types
      $types = sfConfig::get('app_sfAssetsLibrary_types', array(
        'image'   => 'image',
        'txt'     => 'txt',
        'archive' => 'archive',
        'pdf'     => 'pdf',
        'xls'     => 'xls',
        'doc'     => 'doc',
        'ppt'     => 'ppt',
      ));
      $this->widgetSchema['type'] = new sfWidgetFormChoice(array('choices' => $types));
      $this->validatorSchema['type'] = new sfValidatorChoice(array(
        'choices' => array_keys($types),
      ));

      // formatter (see sfWidgetFormSchemaFormatterAsset.class.php)
      $this->widgetSchema->setFormFormatterName('assets');
    }
  }

  /**
   * save
   * @param PropelPDO $con
   */
  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }
    $this->updateObject();
    if ($this->getObject()->isNew())
    {
      $file = $this->getValue('file');
      $this->getObject()->setAuthor($this->getOption('author'));
      $this->getObject()->setFilename($file->getOriginalName());
      if ($this->getValue('description') == '')
      {
        $this->getObject()->setDescription($file->getOriginalName());
      }
      $this->getObject()->create($file->getTempName());
    }
    $this->getObject()->save($con);
    // embedded forms
    $this->saveEmbeddedForms($con);
  }
}