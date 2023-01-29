<?php

namespace Drupal\ap_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides the schedule_appointment pane.
 *
 * @CommerceCheckoutPane(
 *   id = "schedule_appointment",
 *   label = @Translation("Schedule appointment"),
 *   default_step = "schedule_appointment",
 * )
 */
class CustomCheckoutPane extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) { dpm('hoolihan');
    $pane_form['schedule_appointment'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Select pickup date'),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#date_timezone' => 'America/Los_Angeles',
    ];
    $pane_form['schedule_window'] = [
      '#type' => 'select',
      '#title' => t('Select pickup window'),
      '#options' => [
        '8' => t('8am-9am'),
        '9' => t('9am-10am'),
        '10' => t('10am-11am'),
        '11' => t('11am-12pm'),
        '12' => t('12pm-1pm'),
        '1' => t('1pm-2pm'),
        '2' => t('2pm-3pm'),
        '3' => t('3pm-4pm'),
      ],
      '#default_value' => 'morning',
      '#weight' => 5,
    ];
    $pane_form['actions']['schedule'] = [
      '#type' => 'submit',
      '#value' => $this->t('Schedule appointment'),
      '#name' => 'checkout_completion_schedule',
    ];

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $values = $form_state->getValue($pane_form['#parents']);
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $values = $form_state->getValue($pane_form['#parents']);
  }

}