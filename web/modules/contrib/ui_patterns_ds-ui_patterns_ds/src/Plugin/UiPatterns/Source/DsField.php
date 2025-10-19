<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_ds\Plugin\UiPatterns\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\SourcePluginBase;

/**
 * Display suite special field source.
 */
#[Source(
  id: 'ds_field',
  label: new TranslatableMarkup('DS Feild'),
  description: new TranslatableMarkup('Formatted display suite field.'),
  prop_types: ['slot']
)]
class DsField extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    // @todo fetch value from mapped field.
    return [
      "#plain_text" => "This is a DS field",
    ];

  }

}
