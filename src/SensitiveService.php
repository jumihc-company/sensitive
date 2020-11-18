<?php
/**
 * User: YL
 * Date: 2020/11/18
 */

namespace Jmhc\Sensitive;

use DfaFilter\Exceptions\PdsSystemException;

/**
 * 敏感词服务
 * @package Jmhc\Sensitive
 */
class SensitiveService
{
    /**
     * 是否读取文件
     * @var bool
     */
    private $isReadFile = false;

    /**
     * 敏感词
     * @var array
     */
    protected $sensitiveWords = [];

    /**
     * 排除的敏感词
     * @var array
     */
    protected $exceptWords = [];

    /**
     * 词库敏感词
     * @var array
     */
    protected $libraryWords = [];

    /**
     * 使用敏感词库
     * @var bool
     */
    protected $withSensitiveWordLibrary = true;

    /**
     * 设置敏感字符串
     * @param array $words
     * @param bool $isReset
     * @return $this
     */
    public function setSensitiveWords(array $words, bool $isReset = false)
    {
        $this->sensitiveWords = $isReset ? $words : array_merge($this->sensitiveWords, $words);

        return $this;
    }

    /**
     * 设置排除字符串
     * @param array $words
     * @param bool $isReset
     * @return $this
     */
    public function setExceptWords(array $words, bool $isReset = false)
    {
        $this->exceptWords = $isReset ? $words : array_merge($this->exceptWords, $words);

        return $this;
    }

    /**
     * 使用敏感词库
     * @param bool $with
     * @return $this
     */
    public function withSensitiveWordLibrary(bool $with)
    {
        $this->withSensitiveWordLibrary = $with;

        return $this;
    }

    /**
     * 验证
     * @param string $str
     * @param string $prefix
     * @throws PdsSystemException
     * @throws SensitiveException
     */
    public function validate(string $str, string $prefix = '')
    {
        // 敏感词辅助类
        $sensitive = $this->getSensitiveHelper();

        // 设置敏感词
        $sensitive->setTree($this->libraryWords, true);
        $sensitive->setTree($this->sensitiveWords);

        // 获取文字中的敏感词组
        $badWords = $sensitive->getBadWord(str_replace(' ', '', $str));
        $badWords = array_values(array_unique($badWords));
        if (! $badWords) {
            return;
        }

        // 存在敏感数据
        throw new SensitiveException(ucfirst(sprintf(
            '%s%s%s',
            $prefix,
            'contain sensitive words:',
            implode(',', $badWords)
        )), 400, $badWords);
    }

    /**
     * 获取敏感词辅助类
     * @return SensitiveHelper
     */
    protected function getSensitiveHelper() : SensitiveHelper
    {
        // 敏感词实例
        $sensitive = SensitiveHelper::init();

        // 设置排除字符串
        $sensitive->setExcept($this->exceptWords);

        // 已经读取过或不使用敏感词库
        if ($this->isReadFile || ! $this->withSensitiveWordLibrary) {
            return $sensitive;
        }

        // 从文件写入敏感词
        foreach (glob(__DIR__ . '/../words/*.txt') as $file) {
            foreach ($sensitive->yieldToReadFile($file) as $word) {
                $this->libraryWords[] = trim($word);
            }
        }
        $this->libraryWords = array_values(array_unique($this->libraryWords));
        $this->isReadFile = true;

        return $sensitive;
    }
}