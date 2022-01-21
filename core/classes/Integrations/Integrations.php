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

    private DB $_db;
    private array $_integrations = [];
    
    private function db(): DB {
        return $this->_db ??= DB::getInstance();
    }

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
     * @return IntegrationBase|null Instance of integration with same name, null if it doesnt exist.
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

    /**
     * Save a new user linked to a specific integration.
     *
     * @param User $user The user to link
     * @param IntegrationBase $integration The integration to link
     * @param string $identifier The id of the integration account
     * @param string $username The username of the integration account
     * @param string $verified Verified the ownership of the integration account
     * @param string|null $code (optional) The verification code to verify the ownership
     *
     * @return bool
     */
    public function linkIntegrationForUser(User $user, IntegrationBase $integration, string $identifier, string $username, bool $verified = false, string $code = null): bool {
        // Check if user is not already linked to this integration
        if (array_key_exists($integration->data()->id, $user->getConnectedIntegrations())) {
            return false;
        }
        
        $this->db()->createQuery(
            'INSERT INTO nl2_user_integrations (user_id, integration_id, identifier, username, verified, date, code) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                $user->data()->id,
                $integration->data()->id,
                Output::getClean($identifier),
                Output::getClean($username), 
                $verified ? 1 : 0,
                date('U'),
                $code
            ]
        );
        
        return true;
    }

    /**
     * Delete a user's integration data.
     *
     * @param User $user The user to unlink from
     * @param IntegrationBase $integration The integration to unlink
     *
     * @return bool
     */
    public function unlinkIntegrationForUser(User $user, IntegrationBase $integration): bool {
        // Check if user is linked to integration
        if (!array_key_exists($this->data()->id, $user->getConnectedIntegrations())) {
            return false;
        }
        
        $this->db()->createQuery(
            'DELETE FROM nl2_user_integrations WHERE user_id = ? AND integration_id = ?', [
                $user->data()->id,
                $integration->data()->id,
            ]
        );
        
        return true;
    }
}