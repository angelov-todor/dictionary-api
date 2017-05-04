<?php
declare(strict_types=1);

namespace AppBundle\Services;

/**
 * Class for partitioning words into syllables
 * @author Todor Angelov
 */
class Syllables extends Word
{
    const vowel = 3;
    const sonorous = 2;
    const noise = 1;
    const delimiter = '-';

    /**
     * Collection of all vowels
     * @var array
     */
    private static $vowels = [
        "а",
        "е",
        "и",
        "о",
        "у",
        "ъ",
        "^",
        "э",
        "%",
        "@",
        "А",
        "Е",
        "И",
        "О",
        "У",
        "Ъ",
        "ё",
        "ї",
        "я",
        "Я",
        "ю",
        "Ю",
        "Ə",
        "ọ",
        "ö"
    ];

    /**
     * Collection of all noisy consonants
     * @var array
     */
    private static $noise = [
        "б",
        "в",
        "г",
        "д",
        "ж",
        "з",
        "s",
        "џ",
        "к",
        "п",
        "с",
        "т",
        "ф",
        "х",
        "ц",
        "ч",
        "ш",
        "b",
        "w",
        "g",
        "d",
        "t",
        "k",
        "p",
        "f",
        "h",
        "j",
        "щ"
    ];

    /**
     * Sonorous consonants
     * @var array
     */
    private static $sonorous = [
        "р",
        "л",
        "м",
        "н",
        "й",
        "l",
        "m",
        "ђ",
        "v",
        "Р",
        "Л",
        "М",
        "Н"
    ];

    /**
     * Special consonant groups that make difference than the ordinary rules
     * ‘здр’ or ‘шк’ or ‘ск’ or ‘скл’ or ‘жд’ or ‘ст’
     * @var array
     */
    private static $specialConsonantGroups = [
        'здр',
        'шк',
        'ск',
        'скл',
        'жд',
        'ст',
        'дж',
        'дз',
    ];

    /**
     * Get the type of given phoneme
     *
     * @param string $phoneme
     * @return int
     * @throws \Exception
     */
    protected static function getType($phoneme)
    {
        if (in_array($phoneme, static::$noise)) {
            return self::noise;
        }
        if (in_array($phoneme, static::$vowels)) {
            return self::vowel;
        }
        if (in_array($phoneme, static::$sonorous)) {
            return self::sonorous;
        }
        throw new \Exception('Unknown letter');
    }

    /**
     * Actual algorithm for splitting the words
     *
     * @param string $word
     * @return string
     * @throws \Exception
     */
    public function processWord($word)
    {
        $chrArray = $this->toArray($word, false);
        $output = '';
        $groupConsonants = array();

        $firstVowel = null;
        $secondVowel = null;

        while ($current = array_shift($chrArray)) {
            //  if the current is not vowel a.k.a. consonant
            if ($this->getType($current) != self::vowel) {
                //  add to the group of consonants
                $groupConsonants[] = $current;
                if (empty($chrArray)) {
                    $output .= implode('', $groupConsonants);
                }
                continue;
            }
            //  proceed only for vowels
            if ($this->getType($current) != self::vowel) {
                continue;
            }
            if (null == $firstVowel) {
                $firstVowel = $current;
            } else {
                $secondVowel = $firstVowel;
                $firstVowel = $current;
            }

            if (empty($groupConsonants)) {
                //second vowel is found and no consonants in between
                $output .= self::delimiter . $current;
            } else {
                if ($firstVowel && $secondVowel) {
                    $output .= $this->processGroupConsonants(implode('', $groupConsonants)) . $current;
                } else {
                    $output .= implode('', $groupConsonants) . $current;
                }

                $groupConsonants = array();
            }
        }

        return ltrim($output, self::delimiter);
    }

    /**
     * Define the position of delimiter in group of consonants
     *
     * @param string $groupConsonants
     * @return string
     */
    protected function processGroupConsonants($groupConsonants)
    {
        //  split the group of consonants to its parts
        $consArray = $this->toArray($groupConsonants, false);

        foreach (self::$specialConsonantGroups as $group) {
            if (0 === strpos($groupConsonants, $group)) {
                return self::delimiter . $groupConsonants;
            }
        }

        //  length is less or equals 1
        if (count($consArray) <= 1) {
            //then boundary is before groupConsonants
            return self::delimiter . $groupConsonants;
        }

        if ((self::noise == $this->getType($consArray[0])  // the first consonant is noisy
            && (self::sonorous == $this->getType($consArray[1])) // and the second one is sonorous
        )
        ) {
            //then boundary is before groupConsonants
            return self::delimiter . $groupConsonants;
        }
        // else boundary is after first consonant in the group
        $output = array_shift($consArray) . self::delimiter . implode('', $consArray);

        return $output;
    }
}