<?php

namespace Drupal\fireworks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * @ConfigEntityType(
 *   id = "fireworks_accordion",
 *   label = @Translation("Accordion"),
 *   handlers = {
 *     "list_builder" = "Drupal\fireworks\Controller\FireworksController",
 *     "form" = {
 *       "add" = "Drupal\fireworks\Form\FireworksAccordionForm",
 *       "edit" = "Drupal\fireworks\Form\FireworksAccordionForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "fireworks_accordion",
 *   admin_permission = "administer fireworks",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "add-form" = "/admin/config/content/fireworks/accordions/add",
 *     "edit-form" = "/admin/config/content/fireworks/accordions/{fireworks_accordion}/edit",
 *     "delete-form" = "/admin/config/content/fireworks/accordions/{fireworks_accordion}/delete",
 *     "collection" = "/admin/config/content/fireworks/accordions"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "items",
 *   }
 * )
 */
class FireworksAccordion extends ConfigEntityBase {

  protected $id;
  protected $label;
  protected $items = [];

  public function getItems() {
    return $this->items;
  }

  public function setItems(array $items) {
    $this->items = $items;
    return $this;
  }
}
