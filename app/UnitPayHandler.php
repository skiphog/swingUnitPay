<?php
require __DIR__ . '/UnitPay.php';

class UnitPayHandler extends UnitPay
{

    public function validateRequest()
    {
        if (!in_array($_SERVER['REMOTE_ADDR'], $this->getConfig('allowed.ip'), true)) {
            throw new \InvalidArgumentException('IP адрес запрещен');
        }

        if (empty($_GET['method']) || empty($_GET['params']) || !is_array($_GET['params'])) {
            throw new \InvalidArgumentException('Отсутсвуют параметры запроса');
        }

        return $this->checkParams($_GET['method'], $_GET['params']);
    }

    protected function checkParams($method, array $params)
    {
        if (!in_array($method, $this->getConfig('allowed.methods'), true)) {
            throw new \InvalidArgumentException('Недопустимый метод =' . $method . '=');
        }

        if (empty($params['signature']) || $params['signature'] !== $this->getSignature($params, $method)) {
            throw new \InvalidArgumentException('Сигнатура не совпадает');
        }

        return true;
    }

    /**
     * Успешный результат обработки
     * @param $message
     * @return string
     */
    public function getResponseSuccess($message)
    {
        return json_encode([
            'result' => [
                'message' => $message
            ]
        ]);
    }

    /**
     * Ошибка обработки
     * @param $message
     * @return string
     */
    public function getResponseError($message)
    {
        return json_encode([
            'error' => [
                'message' => $message
            ]
        ]);
    }


}