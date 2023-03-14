<?php

namespace Drupal\drupaleasy_repositories;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service description.
 */
class DrupaleasyRepositoriesService {

  /**
   * The plugin manager interface.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected PluginManagerInterface $pluginManagerDrupaleasyRepositories;

  /**
   * The configuration factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Constructs a DrupaleasyRepositories object.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager_drupaleasy_repositories
   *   The plugin manager interface.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory interface.
   */
  public function __construct(PluginManagerInterface $plugin_manager_drupaleasy_repositories, ConfigFactoryInterface $config_factory) {
    $this->pluginManagerDrupaleasyRepositories = $plugin_manager_drupaleasy_repositories;
    $this->configFactory = $config_factory;
  }

  /**
   * Get repository URL help text from each enabled plugin.
   *
   * @return string
   *   The help text.
   */
  public function getValidatorHelpText(): string {
    $repository_plugins = [];

    // Determine which of our plugins are enabled.
    $enabled_repository_plugin_ids = $this->configFactory->get('drupaleasy_repositories.settings')->get('repositories') ?? [];

    // Instantiate our enabled plugins.
    foreach ($enabled_repository_plugin_ids as $enabled_repository_plugin_id) {
      if (!empty($enabled_repository_plugin_id)) {
        $repository_plugins[] = $this->pluginManagerDrupaleasyRepositories->createInstance($enabled_repository_plugin_id);
      }
    }

    $help = [];

    // Loop around enabled plugins, calling each's validateHelpText() method.
    /** @var \Drupal\drupaleasy_repositories\DrupaleasyRepositories\DrupaleasyRepositoriesInterface $repository_plugin */
    foreach ($repository_plugins as $repository_plugin) {
      $help[] = $repository_plugin->validateHelpText();
    }

    // Check to see if the "count" is necessary after hook_form_alter is
    // implemented.
    if (count($help)) {
      return implode(' ', $help);
    }

    // Return the help text.
    return '';
  }

}
