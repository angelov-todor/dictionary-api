<?php
declare(strict_types=1);

namespace AppBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use AppBundle\Entity\Rhymeform;

class RhymeformDataProvider implements ItemDataProviderInterface
{

    /**
     * Retrieves an item.
     *
     * @param string $resourceClass
     * @param string|null $operationName
     * @param int|string $id
     * @param array $context
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return object|null
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if (Rhymeform::class !== $resourceClass) {
            throw new ResourceClassNotSupportedException();
        }
        var_dump($context);
        die;
        // Retrieve the blog post item from somewhere
        return new Rhymeform($id);
    }
}