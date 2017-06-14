<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ImageMetadata
 *
 * @ORM\Table(name="image_metadata")
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"create_image_metadata"}}
 * })
 */
class ImageMetadata
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"get_image", "create_image_metadata"})
     */
    protected $id;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="imageMetadata")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

    /**
     *
     * @var Metadata
     *
     * @ORM\ManyToOne(targetEntity="Metadata")
     * @ORM\JoinColumn(name="metadata_id", referencedColumnName="id")
     * @Groups({"get_image", "create_image_metadata"})
     */
    protected $metadata;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string")
     * @Groups({"get_image", "create_image_metadata"})
     */
    protected $value;

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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return ImageMetadata
     */
    public function setValue(string $value): ImageMetadata
    {
        $this->value = $value;
        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image|null $image
     * @return ImageMetadata
     */
    public function setImage(?Image $image): ImageMetadata
    {
        $this->image = $image;
        return $this;
    }

    public function setMetadata(Metadata $metadata): ImageMetadata
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
