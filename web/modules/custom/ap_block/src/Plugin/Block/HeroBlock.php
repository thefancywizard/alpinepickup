<?php

namespace Drupal\ap_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Hero' Block.
 *
 * @Block(
 *   id = "hero_block",
 *   admin_label = @Translation("Hero block"),
 *   category = @Translation("AP Blocks"),
 * )
 */
class HeroBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The media entity storage.
   *
   * @var \Drupal\media\MediaStorage
   */
  protected $mediaStorage;

  /**
   * The media entity view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  protected $mediaViewBuilder;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->mediaStorage = $entity_type_manager->getStorage('media');
    $this->mediaViewBuilder = $entity_type_manager->getViewBuilder('media');
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->configuration;

    $build = [];

    $build['hero_block_title'] = (!empty($config['hero_block_title'])) ? $config['hero_block_title'] : '';
    $build['hero_block_subtitle'] = (!empty($config['hero_block_subtitle'])) ? $config['hero_block_subtitle'] : '';
    $build['hero_button'] = (!empty($config['hero_button'])) ? $config['hero_button'] : '';

    if (isset($config['background_image'])) {
      $bg_image = $this->mediaStorage->load($config['background_image']);
      $bg_image_render = $this->mediaViewBuilder->view($bg_image, 'default');
      $build['background_image'] = $bg_image_render;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['hero_block_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hero block title'),
      '#default_value' => $config['hero_block_title'] ?? '',
    ];

    $form['hero_block_subtitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hero block subtitle'),
      '#default_value' => $config['hero_block_subtitle'] ?? '',
    ];

    $form['background_image'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#title' => $this->t('Background image'),
      '#default_value' => $config['background_image'],
      '#description' => $this->t('Upload or select your background image.'),
    ];

    $form['hero_button'] = [
      '#type' => 'multibutton',
      '#title' => $this->t('Hero button(s)'),
      '#default_value' => $config['hero_button'],
      '#description' => $this->t('Add buttons to the hero block.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();

    $this->configuration['background_image'] = $values['background_image'];
    $this->configuration['hero_button'] = $values['hero_button'];
    $this->configuration['hero_block_title'] = $values['hero_block_title'];
    $this->configuration['hero_block_subtitle'] = $values['hero_block_subtitle'];
  }
}
