<?php

use_helper('Url');

function auto_wrap_text($text)
{
  return preg_replace('/([_\-\.])/', '<span class="wrap_space"> </span>$1<span class="wrap_space"> </span>', $text);
  //return wordwrap($text, 2, '<span class="wrap_space"> </span>', true);
}

/**
 * Gives an image tag for an asset
 *
 * @param  sfAsset $asset
 * @param  string  $thumbType
 * @param  array   $options
 * @param  string  $relativePath
 * @return string
 */
function asset_image_tag($asset, $thumbType = 'full', $options = array(), $relativePath = null)
{
  $options = array_merge(array(
    'alt'   => $asset->getDescription() . ' ' . $asset->getCopyright(),
    'title' => $asset->getDescription() . ' ' . $asset->getCopyright()
  ), $options);

  if ($asset->isImage())
  {
    $src = $asset->getUrl($thumbType, $relativePath);
  }
  else
  {
    if ($thumbType == 'full')
    {
      throw new sfAssetException('Impossible to render a non-image asset in an image tag');
    }
    else
    {
      switch ($asset->getType())
      {
        case 'txt':
          $src = '/sfAssetsLibraryPlugin/images/txt.png';
          break;
        case 'xls':
          $src = '/sfAssetsLibraryPlugin/images/xls.png';
          break;
        case 'doc':
          $src = '/sfAssetsLibraryPlugin/images/doc.png';
          break;
        case 'pdf':
          $src = '/sfAssetsLibraryPlugin/images/pdf.png';
          break;
        case 'html':
          $src = '/sfAssetsLibraryPlugin/images/html.png';
          break;
        case 'archive':
          $src = '/sfAssetsLibraryPlugin/images/archive.png';
          break;
        case 'bin':
          $src = '/sfAssetsLibraryPlugin/images/bin.png';
          break;
        default:
          $src = '/sfAssetsLibraryPlugin/images/unknown.png';
      }
    }
  }
  return image_tag($src, $options);
}

function link_to_asset($text, $path, $options = array())
{
  return str_replace('%2F', '/', link_to($text, $path, $options));
}

/**
 * @param  string  $text
 * @param  sfAsset $asset
 * @return string
 */
function link_to_asset_action($text, $asset)
{
  $user = sfContext::getInstance()->getUser();
  if ($user->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
  {
    switch($user->getAttribute('popup', null, 'sf_admin/sf_asset/navigation'))
    {
      case 1:
        // popup called from a Rich Text Editor (ex: TinyMCE)
        #return link_to($text, '@sf_asset_library_tiny_config?id=' . $asset->getId(), 'title=' . $asset->getFilename());
        throw new sfAssetException('this option should be unused...');
      case 2:
        // popup called from a simple form input (or via input_sf_asset_tag)
        return link_to_function($text, "setImageField('" . $asset->getUrl() . "','" . $asset->getUrl('small') . "'," . $asset->getId() . ')');
    }
  }
  else
  {
    // case : sf view (i.e. module sfAsset, view list)
    return link_to($text, '@sf_asset_library_edit?id=' . $asset->getId(), 'title=' . $asset->getFilename());
  }
}

/**
 * init asset library for use with TinyMCE
 */
function init_asset_library()
{
  sfContext::getInstance()->getEventDispatcher()->connect('response.filter_content', 'insert_asset_popup_url');
  use_javascript('/sfAssetsLibraryPlugin/js/main', 'last');
}

/**
 * called just before content is sent
 * @see init_asset_library()
 * @param  sfEvent $event
 * @return string
 */
function insert_asset_popup_url(sfEvent $event)
{
  $div = '<div id="sf_asset_js_url" style="display:none">' . url_for('@sf_asset_library_list?popup=2') . '</div>';
  $content = $event->getSubject()->getContent();
  $body = strpos($content, '</body>');
  if (false !== $body)
  {
    $content = substr($content, 0, $body) . PHP_EOL . $div . PHP_EOL . substr($content, $body);
  }
  if (sfConfig::get('sf_web_debug'))
  {
    // TODO web debug toolbar is not displayed anymore :-|
  }

  return $content;
}

/**
 * get breadcrumbs
 * @param  string  $path
 * @param  boolean $linkLast
 * @param  string  $action
 * @return string
 */
function assets_library_breadcrumb($path, $linkLast = false, $action = '')
{
  $action = $action ? $action : sfContext::getInstance()->getRequest()->getParameter('action');
  if ($action == 'edit' || $action == 'update')
  {
    $action = 'list';
  }
  $html = '';
  $breadcrumb = explode('/', $path);
  $nb_dirs = count($breadcrumb);
  $current_dir = '';
  $i = 0;
  foreach ($breadcrumb as $dir)
  {
    if (!$linkLast && ($i == $nb_dirs - 1))
    {
      $html .= $dir;
    }
    else
    {
      $current_dir .= $i ? '/' . $dir : $dir;
      $html .= link_to_asset($dir, '@sf_asset_library_' . $action . '?dir=' . $current_dir) . '<span class="crumb">/</span>';
    }
    $i ++;
  }

  return $html;
}

function input_sf_asset_image_tag($name, $options = array())
{
  $dir = sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media');
  return '<a id="sf_asset_input_image" href="#" rel="{url: \'' . url_for('@sf_asset_library_list?dir=' . $dir . '&popup=2') . '\', name: \'' . $name . '\', type: \'' . $options['type'] . '\'}">' .
    image_tag('/sfAssetsLibraryPlugin/images/folder_open', array('alt' => 'Insert Image')) . '</a>' .
    asset_image_tag(new sfAsset, 'small', array('id' => $options['id'] . '_img'));
}