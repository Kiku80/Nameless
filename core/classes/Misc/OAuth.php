<?php

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Wohali\OAuth2\Client\Provider\Discord as DiscordProvider;

class OAuth extends Instanceable {

    public const DISCORD = 'discord';
    public const GOOGLE = 'google';

    public const PAGE_REGISTER = 'register';
    public const PAGE_LOGIN = 'login';

    private DiscordProvider $_discord_provider;
    private GoogleProvider $_google_provider;

    private DB $_db;

    private function db(): DB {
        return $this->_db ??= DB::getInstance();
    }

    /**
     * Determine if OAuth is available if at least one provider is enabled.
     *
     * @return bool If any provider is enabled
     */
    public function isAvailable(): bool {
        foreach ([self::DISCORD, self::GOOGLE] as $provider) {
            if ($this->isSetup($provider)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get an array of provider names and their instances.
     *
     * @param string $page Either "login" or "register" for generating the callback URL
     * @return array Array of provider names and their instances
     */
    public function getProvidersAvailable(string $page): array {
        $providers = [];
        foreach ([self::DISCORD, self::GOOGLE] as $provider_name) {
            if ($this->isSetup($provider_name)) {
                $provider = $this->getProviderInstance($provider_name, $page);

                $providers[$provider_name] = $provider->getAuthorizationUrl([
                    'scope' => [
                        $provider_name === self::DISCORD ? 'identify' : 'openid',
                        'email'
                    ],
                ]);
            }
        }
        return $providers;
    }

    /**
     * Get or create an instance of a specific provider.
     *
     * @param string $provider The provider name
     * @param string $page Either "login" or "register" for generating the callback URL
     * @return AbstractProvider The provider instance
     */
    public function getProviderInstance(string $provider, string $page): AbstractProvider {
        [$clientId, $clientSecret] = $this->getCredentials($provider);
        $url = rtrim(Util::getSelfURL(), '/') . URL::build("/$page/oauth", "provider=$provider");
        $options = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $url,
        ];

        switch ($provider) {
            case self::DISCORD:
                return $this->_discord_provider ??= new DiscordProvider($options);

            case self::GOOGLE:
                return $this->_google_provider ??= new GoogleProvider($options);

            default:
                throw new RuntimeException('Unknown provider');
        }
    }

    /**
     * Determine if a provider is enabled (different from setup!).
     *
     * @param string $provider The provider name
     * @return bool If the provider is enabled
     */
    public function isEnabled(string $provider): bool {
        return $this->db()->get('oauth', ['provider', '=', $provider])->first()->enabled == '1';
    }

    /**
     * Set a provider as enabled or disabled (`1` or `0` respectively).
     *
     * @param string $provider The provider name
     * @param int $enabled Whether to enable or disable the provider
     */
    public function setEnabled(string $provider, int $enabled): void {
        $this->db()->createQuery("UPDATE nl2_oauth SET enabled = ? WHERE provider = ?", [$enabled, $provider]);
    }

    /**
     * Determine if a provider is setup.
     * A provider is considered setup if it has a client ID and a client secret set.
     *
     * @param string $provider The provider name
     * @return bool If the provider is setup
     */
    public function isSetup(string $provider): bool {
        if (!$this->isEnabled($provider)) {
            return false;
        }

        [$client_id, $client_secret] = $this->getCredentials($provider);

        return $client_id !== '' && $client_secret !== '';
    }

    /**
     * Get the array key for a specific providers client ID.
     * Discord uses `id` and Google uses `sub`, so we have to be able to differentiate.
     *
     * @param string $provider The provider name
     * @return string The array key for the provider's client ID
     */
    public function getIdName(string $provider): string {
        switch ($provider) {
            case self::DISCORD:
                return 'id';
            case self::GOOGLE:
                return 'sub';
            default:
                throw new RuntimeException('Unknown provider');
        }
    }

    /**
     * Get the client ID and client secret for a specific provider.
     *
     * @param string $provider The provider name
     * @return array The configured credentials for this provider
     */
    public function getCredentials(string $provider): array {
        $data = $this->db()->get('oauth', ['provider', '=', $provider])->first();
        return [
            $data->client_id,
            $data->client_secret,
        ];
    }

    /**
     * Update the client ID and client secret for a specific provider.
     *
     * @param string $provider The provider name
     * @param string $client_id The new client ID
     * @param string $client_secret The new client secret
     */
    public function setCredentials(string $provider, string $client_id, string $client_secret): void {
        $this->db()->createQuery(
            "UPDATE nl2_oauth SET client_id = ?, client_secret = ? WHERE provider = ?",
            [$client_id, $client_secret, $provider]
        );
    }

    /**
     * Check if a NamelessMC user has already connected their account to a specific provider.
     *
     * @param string $provider The provider name
     * @param string $provider_id The provider user ID
     * @return bool Whether the user is already linked to the provider
     */
    public function userExistsByProviderId(string $provider, string $provider_id): bool {
        return $this->db()->selectQuery('SELECT user_id FROM nl2_oauth_users WHERE provider = ? AND provider_id = ?', [$provider, $provider_id])->count() > 0;
    }

    /**
     * Get the NamelessMC user ID for a specific provider user ID.
     *
     * @param string $provider The provider name
     * @param string $provider_id The provider user ID for lookup
     * @return int The NamelessMC user ID of the user linked to the provider
     */
    public function getUserIdFromProviderId(string $provider, string $provider_id): int {
        return $this->db()->selectQuery('SELECT user_id FROM nl2_oauth_users WHERE provider = ? AND provider_id = ?', [$provider, $provider_id])->first()->user_id;
    }

    /**
     * Save a new user linked to a specific provider.
     *
     * @param string $user_id The NamelessMC user ID
     * @param string $provider The provider name
     * @param string $provider_id  The provider user ID
     */
    public function saveUserProvider(string $user_id, string $provider, string $provider_id): void {
        $this->db()->createQuery("INSERT INTO nl2_oauth_users (user_id, provider, provider_id) VALUES (?, ?, ?)", [$user_id, $provider, $provider_id]);
    }
}
