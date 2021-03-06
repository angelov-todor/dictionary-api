<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


/**
 * Metadata
 *
 * @ORM\Table(name="metadata")
 * @ORM\Entity
 */
class Metadata
{
    const TYPE_NUMBER = 'number';
    const TYPE_TEXT = 'text';
    const TYPE_BOOLEAN = 'bool';

    protected static $types = [
        self::TYPE_NUMBER,
        self::TYPE_TEXT,
        self::TYPE_BOOLEAN
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"get_image", "create_image_metadata", "list_metadata", "get_enrichment"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Groups({"get_image", "create_image_metadata", "list_metadata", "get_enrichment"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="ENUM('number', 'text', 'bool')", nullable=false)
     * @Groups({"get_image", "create_image_metadata", "list_metadata", "get_enrichment"})
     */
    protected $type = self::TYPE_TEXT;

    /**
     * @var Metadata
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Metadata")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @MaxDepth(2)
     * @Groups({"list_metadata"})
     */
    protected $parent;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Metadata
     */
    public function setName(string $name): Metadata
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Metadata
     */
    public function setType(string $type): Metadata
    {
        if (!in_array($type, self::$types)) {
            throw new \InvalidArgumentException('Invalid type given for metadata');
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return null|Metadata
     */
    public function getParent(): ?Metadata
    {
        return $this->parent;
    }

    /**
     * @param Metadata $parent
     * @return Metadata
     */
    public function setParent(?Metadata $parent): Metadata
    {
        $this->parent = $parent;
        return $this;
    }
}