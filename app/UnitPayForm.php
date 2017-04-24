<?php
require __DIR__ . '/UnitPay.php';

class UnitPayForm extends UnitPay
{
    /**
     * Создать форму оплаты
     * @param $account int|string  id Пользователя или Пользователь + Даритель
     * @return array
     * @throws \UnitPayException
     */
    public function buildForm($account)
    {
        $data = [];

        foreach ((array)$this->getConfig('allowed_sum') as $key => $value) {
            $data[$key] = $this->buildUrl($key, $value, $account);
        }

        return $data;
    }

    /**
     * Ссылки для перехода в систму оплаты UnitPay
     * @param $sum int
     * @param $value array
     * @param $account int
     * @return array
     * @throws \UnitPayException
     */
    private function buildUrl($sum, array $value, $account)
    {
        $params = [
            'account'  => $account,
            'currency' => $this->getConfig('currency'),
            'desc'     => $this->getConfig('desc'),
            'sum'      => $sum
        ];

        $params['signature'] = $this->getSignature($params);

        return $this->getUrl(array_shift($value), $params);
    }

    /**
     * @param $key int
     * @param array $params
     * @return array
     * @throws \UnitPayException
     */
    private function getUrl($key, array $params)
    {
        return [
            'desc' => $key,
            'url'  => $this->getConfig('urls.pay') . $this->getConfig('keys.public') . '?' . http_build_query($params)
        ];
    }
}