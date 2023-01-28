<?php

namespace Drupal\ap_checkout\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\MultistepDefault;

/**
 * @CommerceCheckoutFlow(
 *  id = "custom_checkout_flow",
 *  label = @Translation("Alpine Pickup bookable checkout flow"),
 * )
 */
class CustomCheckoutFlow extends MultistepDefault {

    /**
     * {@inheritdoc}
     */
    public function getSteps() {
        $steps = parent::getSteps();
        $schdule_appointment = [
        'schedule_appointment' => [
            'label' => $this->t('Schedule appointment'),
            'has_sidebar' => TRUE,
            'previous_label' => $this->t('Go back'),
          ],
        ];
        $pos = 1;
        return array_merge(array_slice($steps, 0, $pos), $schdule_appointment, array_slice($steps, $pos));
    }

  }