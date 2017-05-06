<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Image
 * @ORM\Table(name="images")
 * @ORM\Entity
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string")
     */
    protected $src;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @var ImageMetadata
     *
     * @ORM\OneToMany(targetEntity="ImageMetadata", mappedBy="image")
     */
    protected $imageMetadata;

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
     * @param string $description
     * @return Image
     */
    public function setDescription(string $description): Image
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function addImageMetadata(Collection $meta): Image
    {
        foreach ($meta as $iMeta) {
            $this->imageMetadata[] = $iMeta;
        }
        return $this;
    }

    public function addMetadata(ImageMetadata $meta): Image
    {
        $this->imageMetadata[] = $meta;
        return $this;
    }

    public function removeImageMetadata(Collection $meta): Image
    {
        foreach ($meta as $iMeta) {
            $this->imageMetadata->removeElement($iMeta);
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getImageMetadata(): ArrayCollection
    {
        return $this->imageMetadata;
    }

    public function __construct()
    {
        $this->imageMetadata = new ArrayCollection();
    }
}