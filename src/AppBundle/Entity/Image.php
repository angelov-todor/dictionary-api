<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Image
 * @ORM\Table(name="images")
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"get_image"}}
 * })
 */
class Image
{
    const IMAGE_LOCATION = 'assets';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"get_image", "get_enrichment"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string")
     * @Groups({"get_image", "get_enrichment"})
     */
    protected $src;

    /**
     * @var ImageMetadata
     *
     * @ORM\OneToMany(targetEntity="ImageMetadata", mappedBy="image", cascade={"remove"}, orphanRemoval=true)
     * @Groups({"get_image"})
     */
    protected $imageMetadata;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $data;

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Image
     */
    public function setFilename(string $filename): Image
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return Image
     */
    public function setData(string $data): Image
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * @param string $src
     * @return Image
     */
    public function setSrc(string $src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * @param ImageMetadata $imageMetadata
     * @return Image
     */
    public function addImageMetadata(ImageMetadata $imageMetadata): Image
    {
        $imageMetadata->setImage($this);
        $this->imageMetadata->add($imageMetadata);
        return $this;
    }

    /**
     * @param ImageMetadata $imageMetadata
     * @return Image
     */
    public function removeImageMetadata(ImageMetadata $imageMetadata): Image
    {
        $this->imageMetadata->removeElement($imageMetadata);
        $imageMetadata->setImage(null);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getImageMetadata(): Collection
    {
        return $this->imageMetadata;
    }

    /**
     * Image constructor.
     */
    public function __construct()
    {
        $this->imageMetadata = new ArrayCollection();
    }
}