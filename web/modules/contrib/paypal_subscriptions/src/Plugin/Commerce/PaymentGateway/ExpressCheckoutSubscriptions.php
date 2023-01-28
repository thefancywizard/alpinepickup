<?php

namespace Drupal\paypal_subscriptions\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_paypal\IPNHandlerInterface;
use Drupal\commerce_price\RounderInterface;
use Drupal\commerce_price\MinorUnitsConverterInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity\User;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_paypal\Plugin\Commerce\PaymentGateway\ExpressCheckout;

/**
 * Provides the Paypal Express Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "paypal_express_checkout_subscription",
 *   label = @Translation("PayPal recurring (Express Checkout)"),
 *   display_label = @Translation("PayPal"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_paypal\PluginForm\ExpressCheckoutForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "discover", "mastercard", "visa",
 *   },
 *   requires_billing_information = FALSE,
 * )
 */
class ExpressCheckoutSubscriptions extends ExpressCheckout {

  use StringTranslationTrait;
  /**
   * Gets the current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new ExpressCheckoutSubscriptions object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_payment\PaymentTypeManager $payment_type_manager
   *   The payment type manager.
   * @param \Drupal\commerce_payment\PaymentMethodTypeManager $payment_method_type_manager
   *   The payment method type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time.
   * @param \Drupal\paypal_price\MinorUnitsConverterInterface $minor_units_converter
   *   The minor units converter.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_channel_factory
   *   The logger channel factory.
   * @param \GuzzleHttp\ClientInterface $client
   *   The client.
   * @param \Drupal\commerce_price\RounderInterface $rounder
   *   The price rounder.
   * @param \Drupal\commerce_paypal\IPNHandlerInterface $ip_handler
   *   The IPN handler.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PaymentTypeManager $payment_type_manager, PaymentMethodTypeManager $payment_method_type_manager, TimeInterface $time, MinorUnitsConverterInterface $minor_units_converter, LoggerChannelFactoryInterface $logger_channel_factory, ClientInterface $client, RounderInterface $rounder, IPNHandlerInterface $ip_handler, ModuleHandlerInterface $module_handler, EventDispatcherInterface $event_dispatcher, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $payment_type_manager, $payment_method_type_manager, $time, $minor_units_converter, $logger_channel_factory, $client, $rounder, $ip_handler, $module_handler, $event_dispatcher);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.commerce_payment_type'),
      $container->get('plugin.manager.commerce_payment_method_type'),
      $container->get('datetime.time'),
      $container->get('commerce_price.minor_units_converter'),
      $container->get('logger.factory'),
      $container->get('http_client'),
      $container->get('commerce_price.rounder'),
      $container->get('commerce_paypal.ipn_handler'),
      $container->get('module_handler'),
      $container->get('event_dispatcher'),
      $container->get('current_user')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['billing_period'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Billing Period'),
      '#options' => [
        'Day' => $this->t('Day'),
        'Week' => $this->t('Week'),
        'SemiMonth' => $this->t('SemiMonth'),
        'Month' => $this->t('Month'),
        'Year' => $this->t('Year'),
      ],
      '#default_value' => $this->configuration['billing_period'] ?? 'Day',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
    if (!$form_state->getErrors() && $form_state->isSubmitted()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['billing_period'] = $values['billing_period'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['billing_period'] = $values['billing_period'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setExpressCheckout(PaymentInterface $payment, array $extra) {
    $order = $payment->getOrder();
    $amount = $this->rounder->round($payment->getAmount());
    // Build a name-value pair array for this transaction.
    $nvp_data = [
      'METHOD' => 'SetExpressCheckout',
      // Default the Express Checkout landing page to the Mark solution.
      'VERSION' => '124.0',
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
      'PAYMENTREQUEST_0_AMT' => $amount->getNumber(),
      'PAYMENTREQUEST_0_CURRENCYCODE' => $amount->getCurrencyCode(),
      'PAYMENTREQUEST_0_DESC' => 'test EC payment',
      'L_BILLINGTYPE0' => 'RecurringPayments',
      'L_BILLINGAGREEMENTDESCRIPTION0' => 'Subscription',
      // Set the return and cancel URLs.
      'RETURNURL' => $extra['return_url'],
      'CANCELURL' => $extra['cancel_url'],
    ];

    // Add itemized information to the API request.
    $nvp_data += $this->itemizeOrder($order, $amount->getCurrencyCode());

    // Send the order's email if not empty.
    if (!empty($order->getEmail())) {
      $nvp_data['PAYMENTREQUEST_0_EMAIL'] = $order->getEmail();
    }
    // Make the PayPal NVP API request.
    return $this->doRequest($nvp_data, $order);
  }

  /**
   * {@inheritdoc}
   */
  public function getExpressCheckoutDetails(OrderInterface $order) {
    // Get the Express Checkout order token.
    $order_express_checkout_data = $order->getData('paypal_express_checkout');
    // Build a name-value pair array to obtain buyer information from PayPal.
    $nvp_data = [
      'METHOD' => 'GetExpressCheckoutDetails',
      'TOKEN' => $order_express_checkout_data['token'],
    ];
    // Make the PayPal NVP API request.
    return $this->doRequest($nvp_data, $order);
  }

  /**
   * {@inheritdoc}
   */
  public function doExpressCheckoutDetails(OrderInterface $order) {
    // Build NVP data for PayPal API request.
    $account = User::load($this->currentUser->id());
    $order_express_checkout_data = $order->getData('paypal_express_checkout');
    $amount = $this->rounder->round($order->getTotalPrice());
    $configuration = $this->getConfiguration();
    $nvp_data = [
      'METHOD' => 'CreateRecurringPaymentsProfile',
      'SUBSCRIBERNAME' => $account->get('name')->value,
      'PROFILEREFERENCE' => $order->getOrderNumber(),
      'AUTOBILLOUTAMT' => 'AddToNextBilling',
      'PAYERSTATUS' => 'verified',
      'TOKEN' => $order_express_checkout_data['token'],
      'PAYERID' => $order_express_checkout_data['payerid'],
      "PROFILESTARTDATE" => date("Y-m-d\TH:i:s\Z"),
      "DESC" => "Subscription",
      "BILLINGPERIOD" => $configuration['billing_period'],
      "BILLINGFREQUENCY" => "1",
      "AMT" => $amount->getNumber(),
      "CURRENCYCODE" => $amount->getCurrencyCode(),
    ];
    // Make the PayPal NVP API request.
    return $this->doRequest($nvp_data, $order);
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
    $order_express_checkout_data = $order->getData('paypal_express_checkout');
    if (empty($order_express_checkout_data['token'])) {
      throw new PaymentGatewayException('Token data missing for this PayPal Express Checkout transaction.');
    }
    // GetExpressCheckoutDetails API Operation (NVP).
    // Shows information about an Express Checkout transaction.
    $paypal_response = $this->getExpressCheckoutDetails($order);
    // If the request failed, exit now with a failure message.
    if ($paypal_response['ACK'] == 'Failure') {
      throw new PaymentGatewayException($paypal_response['PAYMENTREQUESTINFO_0_LONGMESSAGE'], $paypal_response['PAYMENTREQUESTINFO_n_ERRORCODE']);
    }
    // Set the Payer ID used to finalize payment.
    $order_express_checkout_data['payerid'] = $paypal_response['PAYERID'];
    $order->setData('paypal_express_checkout_recurring', $order_express_checkout_data);
    // If the user is anonymous, add their PayPal e-mail to the order.
    if (empty($order->mail)) {
      $order->setEmail($paypal_response['EMAIL']);
    }
    $order->save();
    // DoExpressCheckoutPayment API Operation (NVP).
    // Completes an Express Checkout transaction.
    $paypal_response = $this->doExpressCheckoutDetails($order);
    // Nothing to do for failures for now - no payment saved.
    // @todo - more about the failures.
    if ($paypal_response['PROFILESTATUS'] !== 'ActiveProfile') {
      throw new PaymentGatewayException($paypal_response['PAYMENTINFO_0_LONGMESSAGE'], $paypal_response['PAYMENTINFO_0_ERRORCODE']);
    }
    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
      'state' => 'authorization',
      'amount' => $order->getTotalPrice(),
      'payment_gateway' => $this->entityId,
      'order_id' => $order->id(),
      'remote_id' => $paypal_response['PROFILEID'],
      'remote_state' => $paypal_response['PROFILESTATUS'],
    ]);

    $payment->save();
  }

}
