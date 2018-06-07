<?php

namespace Drupal\jango_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "nd_parallax",
 *   title = @Translation("Parallax block"),
 *   description = @Translation("Parallax block."),
 *   icon = "fa fa-minus",
 *   description_field = "title",
 * )
 */

class ParallaxBlockShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $output = $text;
    $url = '';
    if (isset($attrs['fid']) && !empty($attrs['fid'])) {
      $uri = File::load($attrs['fid'])->getFileUri();
      $url = file_create_url($uri);
    }

    $content = (isset($attrs['content_align']) && $attrs['content_align'] == 'c-content-left') ? 'left' : 'right';
    $image = (isset($attrs['content_align']) && $attrs['content_align'] == 'c-content-left') ? 'right' : 'left';

    switch ($attrs['type']) {
      case 'c-bg-parallax':
        $attrs['style_background_image'] = $attrs['fid'];
        $attrs['class'] = 'c-content-box c-size-lg c-bg-parallax';
        $output = '<div ' . _jango_shortcodes_shortcode_attributes($attrs) . '>' . $text . '</div>';
        break;

      case 'c-bg-parallax c-feature-bg c-diagonal':
        $attrs['class'] = 'c-content-box c-size-md c-no-padding c-bg-' . $attrs['bg_color'];
        $theme_array = [
          '#theme' => 'jango_shortcodes_parallax_block_diagonal',
          '#image' => $image,
          '#content' => $content,
          '#bg_color' => $attrs['bg_color'],
          '#url' => $url,
          '#text' => $text,
        ];

        $output  = '<div ' . _jango_shortcodes_shortcode_attributes($attrs) . '>';
        $output .= $this->render($theme_array);
        $output .= '</div>';
        break;

      case 'c-bg-parallax c-feature-bg c-arrow':
        $theme_array = [
          '#theme' => 'jango_shortcodes_parallax_block_arrow',
          '#bg_color' => $attrs['bg_color'],
          '#image' => $image,
          '#content' => $content,
          '#url' => $url,
          '#text' => $text,
        ];
        $output  = '<div ' . _jango_shortcodes_shortcode_attributes($attrs) . ' class="c-content-box c-size-md c-no-padding c-bg-' . $attrs['bg_color'] . '">';
        $output .= $this->render($theme_array);
        $output .= '</div>';
        break;

      case 'c-bg-parallax c-feature-bg c-semi-circle':
        $theme_array = [
          '#theme' => 'jango_shortcodes_parallax_block_circle',
          '#image' => $image,
          '#url' => $url,
          '#content' => $content,
          '#text' => $text,
        ];
        $output  = '<div ' . _jango_shortcodes_shortcode_attributes($attrs) . ' class="c-content-box c-size-md c-no-padding c-bg-' . $attrs['bg_color'] . '">';
        $output .= $this->render($theme_array);
        $output .= '</div>';
        break;
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function settings($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $type = [
      'c-bg-parallax' => 'Default parallax',
      'c-bg-parallax c-feature-bg c-diagonal' => 'Parallax block diagonal line',
      'c-bg-parallax c-feature-bg c-arrow' => 'Parallax block arrow',
      'c-bg-parallax c-feature-bg c-semi-circle' => 'Parallax block semi circle',
    ];
    $form['type'] = [
      '#type' => 'select',
      '#options' => $type,
      '#title' => t('Type'),
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'carousel',
      '#attributes' => ['class' => ['form-control']],
      '#prefix' => '<div class = "row"><div class = "col-sm-4">',
      '#suffix' => '</div>',
    ];

    $content_align = [
      'c-content-left' => 'Content align left',
      'c-content-right' => 'Content align right',
    ];
    $form['content_align'] = [
      '#type' => 'select',
      '#options' => $content_align,
      '#title' => t('Type'),
      '#default_value' => isset($attrs['content_align']) ? $attrs['content_align'] : 'c-content-left',
      '#attributes' => ['class' => ['form-control']],
      '#prefix' => '<div class = "col-sm-4">',
      '#suffix' => '</div></div>',
    ];

    $bg_color = [
      'white' => 'White',
      'dark-2' => 'Dark',
    ];
    $form['bg_color'] = [
      '#type' => 'select',
      '#options' => $bg_color,
      '#title' => t('Background Color'),
      '#default_value' => isset($attrs['bg_color']) ? $attrs['bg_color'] : 'white',
      '#attributes' => ['class' => ['form-control']],
      '#prefix' => '<div class = "row"><div class = "col-sm-4">',
      '#suffix' => '</div>',
    ];

    $form['fid'] = [
      '#type' => 'textfield',
      '#title' => t('Background Image'),
      '#default_value' => isset($attrs['fid']) ? $attrs['fid'] : '',
      '#attributes' => ['class' => ['image-media-upload hidden']],
      '#field_suffix' => '<a href="#" class="media-upload button">' . t('Upload Image') . '</a><a href="#" class="media-remove button">' . t('Remove Image') . '</a>',
      '#prefix' => '<div class="col-sm-4">',
      '#suffix' => '</div></div>',
      '#states' => [
        'invisible' => ['select[name="bg"]' => ['value' => '']],
      ],
    ];
    if (isset($attrs['fid']) && !empty($attrs['fid'])) {
      $file = isset($attrs['fid']) && !empty($attrs['fid']) ? File::load($attrs['fid']) : '';
      if ($file) {
        $filename = $file->getFileUri();
        $filename = ImageStyle::load('medium')->buildUrl($filename);
        $form['fid']['#prefix'] = '<div class="col-sm-4"><div class="image-gallery-upload has_image">';
        $form['fid']['#field_suffix'] = '<div class="preview-image"><img src="' . $filename . '"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') . '</a><a href="#" class="gallery-remove button">' . t('Remove Image') . '</a>';
      }
    }
    return $form;
  }
}
