<?php

namespace app\common\oauth2;

use yii\authclient\OAuth2;

/**
 * Odnoklassniki allows authentication via Odnoklassniki OAuth.
 *
 * @author rincha
 */
class Mailru extends OAuth2 {

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://connect.mail.ru/oauth/authorize';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://connect.mail.ru/oauth/token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://www.appsmail.ru/platform';

    /**
     * @var string Public application key
     */
    public $applicationKey;

    /**
     * @inheritdoc
     */
    protected function initUserAttributes() {
        $accessToken = $this->accessToken;
        $params = [];
        $params['method'] = 'users.getInfo';
        $params['app_id'] = $this->clientId;
        $params['session_key'] = $accessToken->getToken();
        $params['secure'] = '1';
        $params['format'] = 'xml';
        $params['uids'] = $accessToken->getParam('id');
        $params['sig'] = $this->sig($params, $this->clientSecret);
        $res=$this->api('api', 'GET', $params);
        return current($res);
    }

    public function fetchAccessToken($authCode, array $params = array()) {
        $token=parent::fetchAccessToken($authCode, $params);
        $token->setParam('id', $token->getParam('x_mailru_vid'));
        return $token;
    }

        /**
     * Generates a signature
     * @param $vars array
     * @param $secret string
     * @return string
     */
    protected function sig($vars, $secret) {
        ksort($vars);
        $params = '';
        foreach ($vars as $key => $value) {
            if (in_array($key, ['sig'])) {
                continue;
            }
            $params .= "$key=$value";
        }
        return md5($params . $secret);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName() {
        return 'mailru';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle() {
        return 'Mailru';
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
