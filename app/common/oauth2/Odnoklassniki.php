<?php

namespace app\common\oauth2;

use yii\authclient\OAuth2;

/**
 * Odnoklassniki allows authentication via Odnoklassniki OAuth.
 *
 * @author rincha
 */
class Odnoklassniki extends OAuth2 {

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://connect.ok.ru/oauth/authorize';

    /**
     * @var string auth request scope.
     */
    public $scope = 'VALUABLE_ACCESS';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.ok.ru/oauth/token.do';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.ok.ru';

    /**
     * @var string Public application key
     */
    public $applicationKey;

    /**
     * @inheritdoc
     */
    protected function initUserAttributes() {
        $params = [];
        $params['access_token'] = $this->accessToken->getToken();
        $params['application_key'] = $this->applicationKey;
        $params['format'] = 'JSON';
        $params['sig'] = $this->sig($params, $params['access_token'], $this->clientSecret);
        return $this->api('api/users/getCurrentUser', 'GET', $params);
    }

    /**
     * @inheritdoc
     */
    /*protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        $params['access_token'] = $accessToken->getToken();
        $params['application_key'] = $this->applicationKey;
        $params['method'] = str_replace('/', '.', str_replace('api/', '', $url));
        $params['sig'] = $this->sig($params, $params['access_token'], $this->clientSecret);
        return $this->sendRequest($method, $url, $params, $headers);
    }*/

    /**
     * Generates a signature
     * @param $vars array
     * @param $accessToken string
     * @param $secret string
     * @return string
     */
    protected function sig($vars, $accessToken, $secret) {
        ksort($vars);
        $params = '';
        foreach ($vars as $key => $value) {
            if (in_array($key, ['sig', 'access_token'])) {
                continue;
            }
            $params .= "$key=$value";
        }
        return md5($params . md5($accessToken . $secret));
    }

    /**
     * @inheritdoc
     */
    protected function defaultName() {
        return 'odnoklassniki';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle() {
        return 'Odnoklassniki';
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap() {
        return [
            'id' => 'uid'
        ];
    }

}
