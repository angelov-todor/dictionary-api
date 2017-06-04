<?php
declare(strict_types=1);

namespace AppBundle\Entity;

class Enrichment
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $question;

    /**
     * Enrich constructor.
     * @param Image $image
     * @param Metadata $metadata
     * @param string $question
     */
    public function __construct(Image $image, Metadata $metadata, string $question)
    {
        $this->image = $image;
        $this->metadata = $metadata;
        $this->question = $question;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }
}