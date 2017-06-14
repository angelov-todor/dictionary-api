<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Enrichment
 * @package AppBundle\Entity
 * @ApiResource(
 *     collectionOperations={"enrich"={"route_name"="enrich"}}, itemOperations={"get"={"method"="GET"}},
 *     attributes={"normalization_context"={"groups"={"get_enrichment"}}}
 * )
 */
class Enrichment
{
    /**
     * @var Image
     * @Groups({"get_enrichment"})
     */
    protected $image;

    /**
     * @var Metadata
     * @Groups({"get_enrichment"})
     */
    protected $metadata;

    /**
     * @var string
     * @Groups({"get_enrichment"})
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