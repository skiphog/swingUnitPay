<?php
require __DIR__.'/UnitPayException.php';

class UnitPay
{
    /**
     * Настройки в файле config.php
     * @var array
     */
    protected $config;

    /**
     * UnitPay constructor.
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    /**
     * Добавить конфиг
     * @param $path string
     * @return $this
     * @throws \UnitPayException
     */
    public function loadConfig($path)
    {
        $file = __DIR__ . '/../config/' . $path . '.php';

        if (!is_file($file)) {
            throw new \UnitPayException('Файла конфига c именем = ' . $path . ' = не существует');
        }
        /** @noinspection PhpIncludeInspection */
        $this->config = array_merge($this->config, require $file);

        return $this;
    }

    /**
     * Получить параметр из Конфига
     * @param $key string
     * @return mixed
     * @throws \UnitPayException
     */
    public function getConfig($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return $this->getStackArrayConfig($key, $this->config);
    }

    /**
     * Создает SHA-256 сигнатуру
     * @param array $params
     * @param null|string $method
     * @return string
     * @throws \UnitPayException
     */
    protected function getSignature(array $params, $method = null)
    {
        ksort($params);
        unset($params['sign'], $params['signature']);
        $params[] = $this->getConfig('keys.secret');

        if ($method !== null) {
            array_unshift($params, $method);
        }

        return hash('sha256', implode('{up}', $params));
    }

    /**
     *
     * @param $key string
     * @param $config array
     * @return mixed
     * @throws \UnitPayException
     */
    private function getStackArrayConfig($key, $config)
    {
        foreach (explode('.', $key) as $value) {
            if (!array_key_exists($value, $config)) {
                throw new \UnitPayException('Не удалось найти параметр ~ ' . $key . ' ~ в конфиге UnitPay');
            }
            $config = $config[$value];
        }

        return $config;
    }

}