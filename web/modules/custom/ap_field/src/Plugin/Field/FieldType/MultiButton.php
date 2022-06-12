<?php

/**
 * @file
 * Contains \Drupal\ap_field\Plugin\Field\FieldType\MultiButton.
 */

namespace Drupal\ap_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'MultiButton' field type.
 *
 * @FieldType (
 *   id = "multibutton",
 *   label = @Translation("MultiButton"),
 *   description = @Translation("Creates buttons."),
 *   default_widget = "multibutton",
 *   default_formatter = "multibutton"
 * )
 */
class MultiButton extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'button_text' => array(
          'type' => 'text',
          'length' => 255,
        ),
        'button_link' => array(
            'type' => 'varchar',
            'length' => 2048,
        ),
        'button_style' => array(
            'type' => 'text',
            'length' => 255,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value1 = $this->get('button_text')->getValue();
    $value2 = $this->get('button_link')->getValue();
    $value3 = $this->get('button_style')->getValue();
    return empty($value1) && empty($value2) && empty($value3);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Add our properties.
    $properties['button_text'] = DataDefinition::create('string')
      ->setLabel(t('Button text'))
      ->setDescription(t('Text that displays on the button.'));

    $properties['button_link'] = DataDefinition::create('string')
      ->setLabel(t('Button link'))
      ->setDescription(t('URL button will link to.'));

    $properties['button_style'] = DataDefinition::create('string')
      ->setLabel(t('Button style'))
      ->setDescription(t('The styling to be applied to the button.'));

    return $properties;
  }
}