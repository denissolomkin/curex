<?php

namespace AppBundle\Entity;

/**
 * CurrencyRate
 */
class CurrencyRate
{
    /**
     * @var string
     */
    private $rate;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var \DateTime
     */
    private $date;


    /**
     * Set rate
     *
     * @param string $rate
     *
     * @return CurrencyRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set currencyCode
     *
     * @param string $currencyCode
     *
     * @return CurrencyRate
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CurrencyRate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
