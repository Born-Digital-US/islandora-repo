<?php

namespace Drupal\asu_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use asu_islandora_utils\ASUIslandoraUtils;

/**
 * Provides a 'Complex Title' Block for page title purposes.
 *
 * @Block(
 *   id = "asu_complex_title",
 *   admin_label = @Translation("The complex title"),
 *   category = @Translation("Views"),
 * )
 */
class ASUComplexTitle extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    //
    // NOTES: this block should be set to display at the top of the "Breadcrumb"
    // region and be configured to only be used for the "Content types" of:
    //   - asu_repository_item
    //   - collection
    //
    // Since this block should be set to display on node/[nid] pages that are
    // "ASU Repository Item", or possibly "Collection", these should both have
    // the paragraph field that is used for display.
    $current_route = \Drupal::routeMatch();
    if ($current_route->getParameter('node')) {
      $node = $current_route->getParameter('node');
      if (!is_object($node)) {
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($node);
      }
    }
    elseif ($current_route->getParameter('arg_0')) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($current_route->getParameter('arg_0'));
    }
    else {
      return [];
    }

    $node_is_published = ASUIslandoraUtils::asu_islandora_utils_node_is_published($node);
    $first_title = $node->field_title[0];
    $view = ['type' => 'complex_title_formatter'];
    $first_title_view = $first_title->view($view);
    $para_render = \Drupal::service('renderer')->render($first_title_view);

    return [
      '#cache' => ['max-age' => 0],
      'complex_title' => [
        '#type' => 'item',
          '#prefix' => '<h1 class="title' .
          ($node_is_published ? "" : " unpublished_title") . '">',
        '#suffix' => '</h1>',
        '#markup' => ($node_is_published ? '' : '<i class="fa fa-lock"></i>&nbsp;') .
          $para_render,
      ],
    ];
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
