<?php

/**
 * phantomTestPlugin configuration.
 * 
 * @package     phantomTestPlugin
 * @subpackage  config
 * @author      Joshua Spankin Nankin
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class phantomTestPluginConfiguration extends sfPluginConfiguration {
    const VERSION = '1.0.0-DEV';

    /**
     * @see sfPluginConfiguration
     */
    public function initialize() {
        $configFiles = $this->configuration->getConfigPaths('config/phantom.yml');
        $config = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);

        foreach ($config as $module => $values) {
            if (is_array($values)) foreach ($values as $name => $value) {
                sfConfig::set("phantom_{$module}_{$name}", $value);
            }
            else {
                sfConfig::set("phantom_{$module}", $values);
            }
        }
    }

}
