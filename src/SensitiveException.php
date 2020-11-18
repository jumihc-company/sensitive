<?php
/**
 * User: YL
 * Date: 2020/11/18
 */

namespace Jmhc\Sensitive;

use Exception;

/**
 * 敏感词异常
 * @package Jmhc\Sensitive
 */
class SensitiveException extends Exception
{
    protected $data;

    public function __construct(string $message = "", int $code = 0, $data = [])
    {
        $this->setData($data);
        parent::__construct($message, $code, null);
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    public function getData(string $key = '', $default = null)
    {
        if (empty($key)) {
            return $this->data;
        }

        return $this->data[$key] ?? $default;
    }
}