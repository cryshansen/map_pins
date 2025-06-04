<?php


/**
 * @file
 * Contains \Drupal\map_pins\Plugin\Block.
 */
namespace Drupal\map_pins\Plugin\Block;


use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Map Pins' Block.
 *
 * @Block(
 *   id = "map_pins_block",
 *   admin_label = @Translation("Map Pins block"),
 *   category = @Translation("Map Pins Block"),
 * )
 */
class MapPinsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
//taken from https://gist.github.com/davidjguru/ab6c43b02ef3b14b305c538a5175c52e  example and 
//https://www.valuebound.com/resources/blog/creating-a-custom-form-in-a-block-in-two-steps-in-Drupal-8 

    $form = \Drupal::formBuilder()->getForm('Drupal\map_pins\Form\MapPinsForm');
    return $form;
  }

}

