<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class Enrichment
 * @package AppBundle\Entity
 * @ApiResource(
 *     collectionOperations={"images_upload"={"route_name"="images_upload"}}
 * )
 */
class ImageUpload
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $data;

    /**
     * ImageUpload constructor.
     * @param string $filename
     * @param string $data
     */
    public function __construct(string $filename, string $data)
    {
        $this->filename = $filename;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return ImageUpload
     */
    public function setFilename(string $filename): ImageUpload
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
     * @return ImageUpload
     */
    public function setData(string $data): ImageUpload
    {
        $this->data = $data;
        return $this;
    }
}