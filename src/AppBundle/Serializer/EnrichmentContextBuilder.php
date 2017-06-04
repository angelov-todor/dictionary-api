<?php
declare(strict_types=1);

namespace AppBundle\Serializer;

use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnrichmentContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;

    public function __construct(SerializerContextBuilderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Creates a serialization context from a Request.
     *
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function createFromRequest(
        Request $request,
        bool $normalization,
        array $extractedAttributes = null
    ): array {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if ($request->getPathInfo() == '/images-enrich') {
            $context['groups'][] = 'get_enrichment';
        }

        return $context;
    }
}