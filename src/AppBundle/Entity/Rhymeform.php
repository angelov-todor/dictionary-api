<?php
declare(strict_types=1);

namespace AppBundle\Entity;

class Rhymeform
{
    /**
     * @var string
     */
    private $rhymeform;

    /**
     * @var Word
     */
    private $word;

    /**
     * Rhymeform constructor.
     * @param string $rhymeform
     * @param Word $word
     */
    public function __construct(string $rhymeform, Word $word)
    {
        $this->rhymeform = $rhymeform;
        $this->word = $word;
    }

    /**
     * @return string
     */
    public function getRhymeform(): string
    {
        return $this->rhymeform;
    }

    /**
     * @param string $rhymeform
     * @return Rhymeform
     */
    public function setRhymeform(string $rhymeform): Rhymeform
    {
        $this->rhymeform = $rhymeform;
        return $this;
    }

    /**
     * @return Word
     */
    public function getWord(): Word
    {
        return $this->word;
    }

    /**
     * @param Word $word
     * @return Rhymeform
     */
    public function setWord(Word $word): Rhymeform
    {
        $this->word = $word;
        return $this;
    }
}