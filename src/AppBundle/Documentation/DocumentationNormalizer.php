<?php
declare(strict_types=1);

namespace AppBundle\Documentation;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DocumentationNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizerDeferred;

    public function __construct(NormalizerInterface $normalizerDeferred)
    {
        $this->normalizerDeferred = $normalizerDeferred;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $TokenDocumentation = [
            'paths' =>
                [
                    '/images-search' =>
                        [
                            'get' => [
                                'tags' => ['Actions'],
                                'operationId' => 'getToken',
                                'consumes' => 'application/json',
                                'produces' => 'application/json',
                                'summary' => 'Search an image',
                                'parameters' => [

                                ],
                                'responses' => [

                                    401 => ['description' => 'Bad credentials']
                                ]
                            ]
                        ],
                ],
        ];
        $officialDocumentation = $this->normalizerDeferred->normalize($object, $format, $context);
//        return $officialDocumentation;

        return array_merge_recursive($officialDocumentation, $TokenDocumentation);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->normalizerDeferred->supportsNormalization($data, $format);
    }
}