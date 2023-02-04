<?php
/**
 * @file
 * Contains \Drupal\ap_field\Plugin\Field\FieldWidget\MultiButtonWidget.
 */

namespace Drupal\ap_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'MultiButton' widget.
 *
 * @FieldWidget (
 *   id = "multibutton",
 *   label = @Translation("MultiButton widget"),
 *   field_types = {
 *     "multibutton"
 *   }
 * )
 */
class MultiButtonWidget extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $element['button_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Button text'),
      '#default_value' => isset($items[$delta]->button_text) ? $items[$delta]->button_text : NULL,
    );
    $element['button_link'] = array(
      '#type' => 'url',
      '#title' => t('Button link'),
      '#default_value' => isset($items[$delta]->button_link) ? $items[$delta]->button_link : NULL,
    );
    $element['button_style'] = array(
      '#type' => 'select',
      '#title' => t('Button style'),
      '#options' => $this->getButtonStyleOptions(),
      '#default_value' => isset($items[$delta]->button_style) ? $items[$delta]->button_style : NULL,
      '#empty_value' => '',
      '#empty_option' => $this->t('None'),
    );

    // If cardinality is 1, ensure a label is output for the field by wrapping
    // it in a details element.
    if ($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() == 1) {
      $element += array(
        '#type' => 'fieldset',
        '#attributes' => array('class' => array('container-inline')),
      );
    }

    return $element;
  }

  protected function getButtonStyleOptions() {
    
    $options = [
      'dark' => 'Dark background with light text',
      'light' => 'Light background with dark text',
      'sage' => 'Sage background with light text',
    ];
    
    return $options;
  }
}