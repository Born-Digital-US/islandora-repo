<?php

/**
 * @file
 * BentoThisSite
 */
namespace Drupal\repo_bento_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search results from this site' Block.
 *
 * @Block(
 *   id = "bento_this_site_results_block",
 *   admin_label = @Translation("Bento 'this site' search results block"),
 *   category = @Translation("Views"),
 * )
 */
class BentoThisSite extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Read the configuration to see how many results we need to display.
    $config = \Drupal::config('repo_bento_search.bentosettings');
    $num_results = $config->get('num_results') ?: 10;
    // Get the search parameter from the GET url.
    // the url parameter is q as in q=cat
    $search_term = \Drupal::request()->query->get('q');
    $results_json = ($search_term) ?
      \Drupal::service('repo_bento_search.this_site')->getSearchResults($search_term) : '';
    return [
      '#cache' => ['max-age' => 0],
      'lib' => [
        '#attached' => [
          'library' => [
            'repo_bento_search/style',
          ],
        ],
      ],
      '#attributes' => [
        'class' => array(0 => 'bento_box'),
      ],
      '#markup' => '<i>' . $num_results . " results configured</i><br>" .
        "Search term: <b>" . $search_term . "</b>" .
        "<pre>" . print_r($results_json, true) . "</pre>",
    ];
  }

}