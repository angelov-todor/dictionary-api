<?php
declare(strict_types=1);

namespace AppBundle\Entity\Dictionary;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Word
{
    /**
     * @var int The id of this word.
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nameStressed;

    /**
     * @var string
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $nameBroken;

    /**
     * @var string
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $nameCondensed;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $meaning;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $synonyms;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $classification;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $etymology;

    /**
     * @var WordType
     * @ORM\ManyToOne(targetEntity="WordType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var IncorrectForm[]
     * @ORM\OneToMany(targetEntity="IncorrectForm", mappedBy="correctWord")
     */
    private $incorrectForms;

    /**
     * @var DerivativeForm
     * @ORM\OneToMany(targetEntity="DerivativeForm", mappedBy="baseWord")
     */
    private $derivativeForms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->incorrectForms = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Word
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nameStressed
     *
     * @param string $nameStressed
     *
     * @return Word
     */
    public function setNameStressed($nameStressed)
    {
        $this->nameStressed = $nameStressed;

        return $this;
    }

    /**
     * Get nameStressed
     *
     * @return string
     */
    public function getNameStressed()
    {
        return $this->nameStressed;
    }

    /**
     * Set nameBroken
     *
     * @param string $nameBroken
     *
     * @return Word
     */
    public function setNameBroken($nameBroken)
    {
        $this->nameBroken = $nameBroken;

        return $this;
    }

    /**
     * Get nameBroken
     *
     * @return string
     */
    public function getNameBroken()
    {
        return $this->nameBroken;
    }

    /**
     * Set nameCondensed
     *
     * @param string $nameCondensed
     *
     * @return Word
     */
    public function setNameCondensed($nameCondensed)
    {
        $this->nameCondensed = $nameCondensed;

        return $this;
    }

    /**
     * Get nameCondensed
     *
     * @return string
     */
    public function getNameCondensed()
    {
        return $this->nameCondensed;
    }

    /**
     * Set meaning
     *
     * @param string $meaning
     *
     * @return Word
     */
    public function setMeaning($meaning)
    {
        $this->meaning = $meaning;

        return $this;
    }

    /**
     * Get meaning
     *
     * @return string
     */
    public function getMeaning()
    {
        return $this->meaning;
    }

    /**
     * Set synonyms
     *
     * @param string $synonyms
     *
     * @return Word
     */
    public function setSynonyms($synonyms)
    {
        $this->synonyms = $synonyms;

        return $this;
    }

    /**
     * Get synonyms
     *
     * @return string
     */
    public function getSynonyms()
    {
        return $this->synonyms;
    }

    /**
     * Set classification
     *
     * @param string $classification
     *
     * @return Word
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;

        return $this;
    }

    /**
     * Get classification
     *
     * @return string
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set etymology
     *
     * @param string $etymology
     *
     * @return Word
     */
    public function setEtymology($etymology)
    {
        $this->etymology = $etymology;

        return $this;
    }

    /**
     * Get etymology
     *
     * @return string
     */
    public function getEtymology()
    {
        return $this->etymology;
    }

    /**
     * Set type
     *
     * @param WordType $type
     *
     * @return Word
     */
    public function setType(WordType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return WordType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add incorrectForm
     *
     * @param IncorrectForm $incorrectForm
     *
     * @return Word
     */
    public function addIncorrectForm(IncorrectForm $incorrectForm)
    {
        $this->incorrectForms[] = $incorrectForm;

        return $this;
    }

    /**
     * Remove incorrectForm
     *
     * @param IncorrectForm $incorrectForm
     */
    public function removeIncorrectForm(IncorrectForm $incorrectForm)
    {
        $this->incorrectForms->removeElement($incorrectForm);
    }

    /**
     * Get incorrectForms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIncorrectForms()
    {
        return $this->incorrectForms;
    }

    /**
     * Add derivativeForm
     *
     * @param DerivativeForm $derivativeForm
     *
     * @return Word
     */
    public function addDerivativeForm(DerivativeForm $derivativeForm)
    {
        $this->derivativeForms[] = $derivativeForm;

        return $this;
    }

    /**
     * Remove derivativeForm
     *
     * @param DerivativeForm $derivativeForm
     */
    public function removeDerivativeForm(DerivativeForm $derivativeForm)
    {
        $this->derivativeForms->removeElement($derivativeForm);
    }

    /**
     * Get derivativeForms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDerivativeForms()
    {
        return $this->derivativeForms;
    }
}
