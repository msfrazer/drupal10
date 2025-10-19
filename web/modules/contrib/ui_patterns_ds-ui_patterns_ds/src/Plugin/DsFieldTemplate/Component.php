<?php

namespace Drupal\ui_patterns_ds\Plugin\DsFieldTemplate;

use Drupal\Core\Form\FormState;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ds\Plugin\DsFieldTemplate\DsFieldTemplateBase;
use Drupal\ds\Attribute\DsFieldTemplate;
use Drupal\ui_patterns\Form\ComponentFormBuilderTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DS field template plugin for using UI pattern..
 */
#[DsFieldTemplate(
  id: 'ui_pattern_ds_component',
  title: new TranslatableMarkup('FIeld template for UI pattern'),
  theme: 'component',
)]
class Component extends DsFieldTemplateBase implements ContainerFactoryPluginInterface {
  use ComponentFormBuilderTrait;

  /**
   * Constructs a new Component object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(&$form) {
    $config = $this->getConfiguration();
    $form_state = new FormState();
    $this->setComponentConfiguration($config);
    // @todo Load correct context from the entity.
    $context = [];
    $form = array_merge(
      $form,
      $this->buildComponentsForm(
        $form_state,
        $context,
        NULL,
        TRUE,
        TRUE
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return $this->getComponentFormDefault();
  }

  /**
   * Get the field currently being edited.
   *
   * @return string
   *   Name of the field currently being edited.
   */
  protected function getCurrentField(): ?string {
    $request = $this->requestStack->getCurrentRequest()->request;
    $parameters = $request->all();
    if (isset($parameters["fields"]) && is_array($parameters["fields"])) {
      $fields = array_filter($parameters["fields"], function ($field) {
        return isset(
          $field["settings_edit_form"]["third_party_settings"]["ds"]["ft"]["id"]
        ) &&
          $field["settings_edit_form"]["third_party_settings"]["ds"]["ft"]["id"] == "component";
      });
      $fields = array_keys($fields);
      $field = reset($fields);
    }
    if (empty($field)) {
      $trigger_element = $request->get("_triggering_element_name");
      $field = str_replace("_plugin_settings_edit", "", $trigger_element);
    }
    return isset($parameters["fields"][$field]) ? $field : NULL;
  }

}
