<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */
namespace app\common\smsgates;

/**
 * provide sms-gate http://mainsms.ru
 * @author rincha
*/
class Mainsms extends Base
{
    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';



    protected
        $project    = null,
        $key        = null,
        $testMode   = false,
        $url        = 'mainsms.ru/api/mainsms',
        $useSSL     = false,
        $response   = null;

    /**
     * Конструктор
     *
     * @param Array $config
     */
    public function __construct($config)  {
        $this->project = \yii\helpers\ArrayHelper::getValue($config, 'project');
        $this->key = \yii\helpers\ArrayHelper::getValue($config, 'key');
        $this->useSSL = \yii\helpers\ArrayHelper::getValue($config, 'useSSL');
        $this->testMode = \yii\helpers\ArrayHelper::getValue($config, 'testMode');
    }

    /**
     * Отправить SMS
     *
     * @param string|array $recipients
     * @param string $message
     * @param string $sender
     *
     * @return boolean|integer
     * @deprecated
     */
    public function sendSMS($recipients, $message, $sender = null, $run_at = null)
    {
        return $this->messageSend($recipients, $message, $sender, $run_at);
    }

    /**
     * Отправить Пакет SMS
     *
     * @param string $sender
     * @param array $messages [["id"=>$id, "phone"=>$phone, "text"=>$text], [...] , ... ]
     *
     * @return boolean|integer
     * @deprecated
     */

    public function sendBatch($sender, $messages)
    {
        $params = array(
            'messages'       => $messages,
            'sender'        => $sender,
        );

        if ($this->testMode) {
            $params['test'] = 1;
        }

        $response = $this->makeBatchRequest('batch/send', $params);

        return $response['status'] == self::REQUEST_SUCCESS;
    }


    /**
     * Склейка параметров для формирования сигнатуры
     *
     * @param string $function
     * @param array $params
     *
     * @return stdClass
     */

    protected function joinArrayBatchValues($params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            $result[$name] = is_array($value) ? join(',', ( is_array(array_values($value)[0]) ?  $this->joinArrayBatchValues($value) : $value)) : $value;
        }
        return $result;
    }


    /**
     * Отправить запрос с пакетом SMS
     *
     * @param string $function
     * @param array $params
     *
     * @return stdClass
     */

    protected function makeBatchRequest($function, array $params = array())
    {
        $params_for_sign = $this->joinArrayBatchValues($params);
        $sign = $this->generateSign($params_for_sign);
        $params = array_merge(array('project' => $this->project), $params);

        $url = ($this->useSSL ? 'https://' : 'http://') . $this->url .'/'. $function;
        $post = http_build_query(array_merge($params, array('sign' => $sign)), '', '&');

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($this->useSSL) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
                    'timeout' => 10,
                ),
            ));
            $response = file_get_contents($url, false, $context);
        }
        return $this->response = json_decode($response, true);
    }

    /**
     * Проверить статус доставки сообщений
     *
     * @param string|array $messagesId
     *
     * @return boolean|array
     * @deprecated
     */
    public function checkStatus($messagesId)
    {
        return $this->messageStatus($messagesId);
    }

    /**
     * Отправить SMS
     *
     * @param string|array $recipients
     * @param string $message
     * @param string $sender
     * @param string $run_at
     *
     * @return boolean|integer
     */
    public function messageSend($recipients, $message, $sender, $run_at = null)
    {
        $params = array(
            'recipients'    => $recipients,
            'message'       => $message
        );

        if ($sender != null) {
            $params['sender'] = $sender;
        }

        if ($run_at != null) {
            $params['run_at'] = $run_at;
        }

        if ($this->testMode) {
            $params['test'] = 1;
        }

        $response = $this->makeRequest('message/send', $params);

        return $response['status'] == self::REQUEST_SUCCESS;
    }

    /**
     * Проверить статус доставки сообщений
     *
     * @param string|array $messagesId
     *
     * @return boolean|array
     */
    public function messageStatus($messagesId)
    {
        if (! is_array($messagesId)) {
            $messagesId = array($messagesId);
        }

        $response = $this->makeRequest('message/status', array(
            'messages_id' => join(',', $messagesId),
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['messages'] : false;
    }

    /**
     * Запрос стоимости сообщения
     *
     * @param string|array $recipients
     * @param string $message
     *
     * @return boolean|decimal
     */
    public function messagePrice($recipients, $message)
    {
        $response = $this->makeRequest('message/price', array(
            'recipients'    => $recipients,
            'message'       => $message,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['price'] : false;
    }

    /**
     * Запрос информации о номерах
     *
     * @param string|array $recipients
     *
     * @return boolean|decimal
     */
    public function phoneInfo($phones)
    {
        $response = $this->makeRequest('message/info', array(
            'phones'    => $phones
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['info'] : false;
    }


    /**
     * Запросить баланс
     *
     */
    public function userBalance()
    {
        $response = $this->makeRequest('message/balance');
        return $response['status'] == self::REQUEST_SUCCESS ? $response['balance'] : false;
    }

    /**
     * Запросить баланс
     *
     */
    public function getBalance()
    {
        return $this->userBalance();
    }


    /**
     * Отправить запрос
     *
     * @param string $function
     * @param array $params
     *
     * @return stdClass
     */
    protected function makeRequest($function, array $params = array())
    {
        $params = $this->joinArrayValues($params);
        $sign = $this->generateSign($params);
        $params = array_merge(array('project' => $this->project), $params);

        $url = ($this->useSSL ? 'https://' : 'http://') . $this->url .'/'. $function;
        $post = http_build_query(array_merge($params, array('sign' => $sign)), '', '&');

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($this->useSSL) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
                    'timeout' => 10,
                ),
            ));
            $response = file_get_contents($url, false, $context);
        }
        return $this->response = json_decode($response, true);
    }

    protected function joinArrayValues($params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            $result[$name] = is_array($value) ? join(',', ( is_array(array_values($value)[0]) ?  $this->joinArrayValues($value) : $value)) : $value;
        }
        return $result;
    }


    /**
     * Установить адрес шлюза
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * Получить адрес сервера
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }




    /**
     * Возвращает ответ сервера последнего запроса
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * Сгенерировать подпись
     *
     * @param array $params
     * @return string
     */
    protected function generateSign(array $params)
    {
      $params['project'] = $this->project;
      ksort($params);
      return md5(sha1(join(';', array_merge($params, Array($this->key)))));
    }
}
