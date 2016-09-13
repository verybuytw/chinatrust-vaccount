<?php

namespace VeryBuy\Payment\ChinaTrust\VirtualAccount;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator\NoneVerifyCodeGenerator;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator\SingleVerifyCodeGenerator;
use VeryBuy\Payment\ChinaTrust\VirtualAccount\Generator\VerifyCodeGeneratorContract;

class VirtualAccountBuilder
{
    use VirtualAccountConfigValidate;

    /**
     * @var string length:5
     */
    protected $companyId;

    /**
     * @var VerifyCodeGeneratorContract
     */
    protected $generator;

    /**
     * @param int $companyId
     * @param array $config
     */
    public function __construct($companyId, array $config)
    {
        $this->setCompanyId($companyId)
                ->setConfig($config);
    }

    /**
     * @param int $companyId
     *
     * @return VirtualAccountBuilder
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param array $config
     *
     * @return VirtualAccountBuilder
     */
    public function setConfig(array $config)
    {
        $this->config = Collection::make($config);

        return $this->validateConfig()->initVerifyCodeGenerator();
    }

    /**
     * @param string|null $field
     * @param string|null $default
     *
     * @return Collection|mixed
     */
    protected function getConfig($field = null, $default = null)
    {
        if (is_null($field)) {
            return $this->config;
        }

        return $this->config->has($field) ? $this->config->get($field) : $default;
    }

    /**
     * @return VirtualAccountBuilder
     */
    protected function initVerifyCodeGenerator()
    {
        $generator = $this->buildGenerator(static::getConfig('type'));

        return $this->setGenerator($generator);
    }

    /**
     * @param int $type
     *
     * @return SingleVerifyCodeGenerator
     * @throws InvalidArgumentException
     */
    protected function buildGenerator($type)
    {
        if ($type === VerifyType::NONE_BASE) {
            return new NoneVerifyCodeGenerator;
        }

        if (in_array($type, [VerifyType::SINGLE_AMOUNT, VerifyType::SINGLE_AMOUNT_DATE])) {
            return new SingleVerifyCodeGenerator;
        }

        throw new InvalidArgumentException('Generator not found.');
    }

    /**
     * @param VerifyCodeGeneratorContract $generator
     *
     * @return VirtualAccount
     */
    protected function setGenerator(VerifyCodeGeneratorContract $generator)
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @return VerifyCodeGeneratorContract
     */
    protected function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function make()
    {
        switch (static::getConfig('type')) {
            case VerifyType::NONE_BASE:
                return static::getGenerator()->buildWithBase(
                    static::getCompanyId(),
                    static::getConfig('number')
                );
            case VerifyType::SINGLE_AMOUNT:
                return static::getGenerator()->buildWithAmount(
                    static::getCompanyId(),
                    static::getConfig('number'),
                    sprintf('%010s', static::getConfig('amount'))
                );
            case VerifyType::SINGLE_AMOUNT_DATE:
                return static::getGenerator()->buildWithAmountAndDate(
                    static::getCompanyId(),
                    static::getConfig('number'),
                    sprintf('%010s', static::getConfig('amount')),
                    static::getConfig('expired_at')
                );
            default:
                break;
        }

        throw new InvalidArgumentException('Undefined generator.');
    }
}
