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

    $config = $this->getConfiguration(); dpm($config);

    $title = (!empty($config['hero_block_title'])) ? $config['hero_block_title'] : '';
    $subtitle = (!empty($config['hero_block_subtitle'])) ? $config['hero_block_subtitle'] : '';

    if (isset($config['background_image'])) {
      $bg_image = $this->mediaStorage->load($config['background_image']);
      dpm('ponce');
      dpm($bg_image);
      $bg_image_render = $this->mediaViewBuilder->view($bg_image, 'default');
    }

    return [
      '#theme' => 'hero_block',
      '#hero_block_title' => $title,
      '#hero_block_subtitle' => $subtitle,
      '#background_image' => $bg_image_render,
    ];
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

    // $form['hero_block_image'] = [
    //   '#type' => 'managed_file',
    //   '#upload_location' => 'public://',
    //   '#title' => $this->t('Hero image'),
    //   '#theme' => 'image_widget',
    //   '#default_value' => $this->configuration['hero_block_image'],
    //   '#upload_validators' => [
    //     'file_validate_extensions' => ['gif png jpg jpeg'],
    //   ],
    //   '#states' => [
    //     'visible' => [
    //       ':input[name="image_type"]' => ['value' => t('Upload New Image(s)')],
    //     ]
    //   ]
    // ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();

    /* Fetch the array of the file stored temporarily in database */
   // $image = $form_state->getValue('image');

   // $this->configuration['hero_block_image'] = $image;

    /* Load the object of the file by it's fid */
   // $file = \Drupal\file\Entity\File::load($image[0]);

    /* Set the status flag permanent of the file object */
   // $file->setPermanent();

    /* Save the file in database */
   // $file->save();
    $this->configuration['background_image'] = $values['background_image'];
    $this->configuration['hero_block_title'] = $values['hero_block_title'];
    $this->configuration['hero_block_subtitle'] = $values['hero_block_subtitle'];
  }
}
