<?php
/*
 *	Made by Partydragen
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Integrations class
 */

class Integrations extends Instanceable {

    private array $_integrations = [];

    /**
     * Register a integration to the integration list.
     *
     * @param IntegrationBase $integration Instance of intagration to register.
     */
    public function registerIntegration(IntegrationBase $integration): void {
        $this->_integrations[$integration->getName()] = $integration;
    }

    /**
     * Get a integration by name.
     *
     * @param string $name Name of integration to get.
     *
     * @return WidgetBase|null Instance of integration with same name, null if it doesnt exist.
     */
    public function getIntegration(string $name): ?IntegrationBase {
        if (array_key_exists($name, $this->_integrations)) {
            return $this->_integrations[$name];
        }

        return null;
    }

    /**
     * List all integrations, sorted by their order.
     *
     * @return IntegrationBase[] List of integrations.
     */
    public function getAll(): iterable {
        $integrations = $this->_integrations;

        uasort($integrations, static function ($a, $b) {
            return $a->getOrder() - $b->getOrder();
        });

        return $integrations;
    }
}