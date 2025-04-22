<?php

namespace Drupal\fireworks\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityForm;

class FireworksAccordionForm extends EntityForm {

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#required' => true,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\fireworks\Entity\FireworksAccordion::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $items = $entity->getItems() ?: [];
    
    $form['items_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Accordion Items'),
      '#prefix' => '<div id="items-wrapper">',
      '#suffix' => '</div>',
      '#tree' => true,
    ];

    $item_count = $form_state->get('item_count') ?? count($items);
    if ($item_count === 0) {
      $item_count = 1;
      $form_state->set('item_count', $item_count);
    }

    for ($i = 0; $i < $item_count; $i++) {
      $form['items_wrapper']['items'][$i] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Item @num', ['@num' => $i + 1]),
      ];

      $form['items_wrapper']['items'][$i]['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $items[$i]['title'] ?? '',
        '#required' => true,
      ];

      $form['items_wrapper']['items'][$i]['content'] = [
        '#type' => 'text_format',
        '#title' => $this->t('Content'),
        '#default_value' => $items[$i]['content']['value'] ?? '',
        '#format' => $items[$i]['content']['format'] ?? 'full_html',
        '#required' => true,
      ];
    }

    $form['items_wrapper']['actions'] = [
      '#type' => 'actions',
    ];

    $form['items_wrapper']['actions']['add_item'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another item'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'items-wrapper',
      ],
    ];

    if ($item_count > 1) {
      $form['items_wrapper']['actions']['remove_item'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove last item'),
        '#submit' => ['::removeOne'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'items-wrapper',
        ],
      ];
    }

    if (!$entity->isNew()) {
      $form['placeholder'] = [
        '#type' => 'item',
        '#markup' => $this->t('Use the placeholder <strong>###ACCORDION_@id###</strong> in your content to display this accordion.', ['@id' => strtoupper($entity->id())]),
      ];
    }

    return $form;
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['items_wrapper'];
  }

  public function addOne(array &$form, FormStateInterface $form_state) {
    $item_count = $form_state->get('item_count');
    $form_state->set('item_count', $item_count + 1);
    $form_state->setRebuild();
  }

  public function removeOne(array &$form, FormStateInterface $form_state) {
    $item_count = $form_state->get('item_count');
    if ($item_count > 1) {
      $form_state->set('item_count', $item_count - 1);
    }
    $form_state->setRebuild();
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $values = $form_state->getValues();
    if (!isset($values['items_wrapper']['items'])) {
      return;
    }

    foreach ($values['items_wrapper']['items'] as $delta => $item) {
      if (empty($item['title'])) {
        $form_state->setErrorByName(
          "items_wrapper][items][$delta][title",
          $this->t('Title is required for item @number.', ['@number' => $delta + 1])
        );
      }

      if (empty($item['content']['value'])) {
        $form_state->setErrorByName(
          "items_wrapper][items][$delta][content][value",
          $this->t('Content is required for item @number.', ['@number' => $delta + 1])
        );
      }
    }
  }

  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $items = [];

    $values = $form_state->getValues();
    if (!empty($values['items_wrapper']['items'])) {
      foreach ($values['items_wrapper']['items'] as $item) {
        if (!empty($item['title']) && isset($item['content'])) {
          $items[] = [
            'title' => $item['title'],
            'content' => [
              'value' => $item['content']['value'],
              'format' => $item['content']['format'] ?? 'full_html',
            ],
          ];
        }
      }
    }

    $entity->setItems($items);
    $status = $entity->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addMessage($this->t('Created the %label accordion.', [
        '%label' => $entity->label(),
      ]));
    } else {
      $this->messenger()->addMessage($this->t('Saved the %label accordion.', [
        '%label' => $entity->label(),
      ]));
    }

    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }
}
