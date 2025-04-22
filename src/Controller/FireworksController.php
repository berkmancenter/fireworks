<?php

namespace Drupal\fireworks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class FireworksController extends ControllerBase {

  /**
   * Displays a list of fireworks accordions.
   */
  public function listing() {
    $build = [];

    $entities = $this->entityTypeManager()
      ->getStorage('fireworks_accordion')
      ->loadMultiple();

    if (empty($entities)) {
      $build['empty'] = [
        '#markup' => $this->t('No accordions available. <a href=":link">Add accordion</a>.', [
          ':link' => Url::fromRoute('entity.fireworks_accordion.add_form')->toString()
        ]),
      ];
      return $build;
    }

    $header = [
      $this->t('Label'), 
      $this->t('Placeholder'), 
      $this->t('Operations')
    ];

    $rows = [];
    foreach ($entities as $entity) {
      $rows[] = [
        $entity->toLink(NULL, 'edit-form'),
        '###ACCORDION_' . strtoupper($entity->id()) . '###',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => $this->getOperationLinks($entity),
          ],
        ],
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No accordions available.'),
    ];

    $build['add_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Add new accordion'),
      '#url' => Url::fromRoute('entity.fireworks_accordion.add_form'),
      '#attributes' => ['class' => ['button', 'button--primary']],
    ];

    return $build;
  }

  /**
   * Builds operation links for the entity.
   */
  protected function getOperationLinks($entity) {
    $links = [
      'edit' => [
        'title' => $this->t('Edit'),
        'url' => $entity->toUrl('edit-form'),
      ],
      'delete' => [
        'title' => $this->t('Delete'),
        'url' => $entity->toUrl('delete-form'),
      ],
    ];
    return $links;
  }
}
