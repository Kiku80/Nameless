<?php

/*
 *	Made by Partydragen
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Minecraft Integration
 */

class MinecraftIntegration extends IntegrationBase {
    
    public function __construct() {
        $this->_name = 'Minecraft';

        parent::__construct();
    }
    
    public function linkUser(User $user, array $extra_fields = []) {
        // Check if user is not already linked to this integration
        if(array_key_exists($this->data()->id, $user->getConnectedIntegrations())) {
            return false;
        }
        
        $fields = [
            'integration_id' => $this->data()->id,
            'user_id' => $user->data()->id,
            'date' => date('U')
        ];
        
        DB::getInstance()->insert('user_integrations', array_merge($fields, $extra_fields));
    }
    
    public function unlinkUser(User $user) {
    }
}