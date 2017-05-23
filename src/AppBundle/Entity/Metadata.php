<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


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

    protected static $types = [
        self::TYPE_NUMBER,
        self::TYPE_TEXT
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"get_image"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Groups({"get_image"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="ENUM('number', 'text')", nullable=false)
     * @Groups({"get_image"})
     */
    protected $type = self::TYPE_TEXT;

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
}