<?php
declare(strict_types=1);

namespace AppBundle\Services\Word;

interface AdapterInterface
{
    /**
     * @param string $word
     * @return array|null
     */
    public function findWord($word);
}