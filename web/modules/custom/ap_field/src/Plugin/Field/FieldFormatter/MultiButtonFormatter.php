<?php
/**
 * @file
 * Contains \Drupal\ap_field\Plugin\Field\FieldFormatter\MultiButtonFormatter.
 */

namespace Drupal\ap_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'MultiButton' formatter.
 *
 * @FieldFormatter (
 *   id = "multibutton",
 *   label = @Translation("MultiButton"),
 *   field_types = {
 *     "multibutton"
 *   }
 * )
 */
class MultiButtonFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = [];
    
    foreach ($items as $delta => $item) { 
        $item_values = $items[$delta]->getValue();
        $elements[$delta]['button_text'] = $item_values['button_text'];
        $elements[$delta]['button_link'] = $item_values['button_link'];
        $elements[$delta]['button_style'] = $item_values['button_style'];
    }

    return $elements;
  }
}