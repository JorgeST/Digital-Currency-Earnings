<?php


class Currency
{
    public $name;
    public $abbreviation;

    /**
     * Initialize Currency.
     *
     * @param string $name
     * @param string $abbreviation
     */
    public function __construct($abbreviation,$name)
    {
        $this->abbreviation = $abbreviation;
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get abbreviation.
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

}