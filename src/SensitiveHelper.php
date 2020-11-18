<?php
/**
 * User: YL
 * Date: 2020/11/18
 */

namespace Jmhc\Sensitive;

use DfaFilter\HashMap;

/**
 * 敏感词辅助
 * @package Jmhc\Sensitive
 */
class SensitiveHelper extends \DfaFilter\SensitiveHelper
{
    protected static $_instance;

    /**
     * 排除字符串
     * @var array
     */
    protected $exceptWords = [];

    public function __construct()
    {
        // 初始化敏感词树
        $this->wordTree = new HashMap();
    }

    /**
     * {@inheritDoc}
     */
    public static function init()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    /**
     * 设置排除字符串
     * @param array $words
     * @return $this
     */
    public function setExcept(array $words)
    {
        if (! empty($words)) {
            $this->exceptWords = array_merge($this->exceptWords, $words);
        }

        return $this;
    }

    /**
     * 构建敏感词树【数组模式】
     * @param null $sensitiveWords
     * @param bool $isReset
     * @return $this
     */
    public function setTree($sensitiveWords = null, bool $isReset = false)
    {
        if (empty($sensitiveWords)) {
            return $this;
        }

        $this->wordTree = $isReset ? new HashMap() : ($this->wordTree ?: new HashMap());

        foreach ($sensitiveWords as $word) {
            $this->buildWordToTree($word);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function yieldToReadFile($filepath)
    {
        return parent::yieldToReadFile($filepath);
    }

    protected function buildWordToTree($word = '')
    {
        if (in_array($word, $this->exceptWords)) {
            return;
        }

        parent::buildWordToTree($word);
    }
}