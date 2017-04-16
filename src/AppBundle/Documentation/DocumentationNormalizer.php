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
        $allowedFormat = [
            'application/json'
        ];
        $TokenDocumentation = [
            'paths' =>
                [
                    '/authenticate' =>
                        [
                            'post' => [
                                'tags' => ['Authentication'],
                                'operationId' => 'getToken',
                                'consumes' => $allowedFormat,
                                'produces' => $allowedFormat,
                                'summary' => 'Acquire JWT Token.',
                                'parameters' => [
                                    [
                                        'name' => 'user',
                                        'in' => 'body',
                                        'description' => 'Your Login Credentials',
                                        'required' => true,
                                        'schema' => [
                                            "type" => "object",
                                            "properties" => [
                                                "username" => ["type" => "string"],
                                                "password" => ["type" => "string"]
                                            ],
                                            "message" => ["type" => "string"],
                                            "default" => [
                                                "username" => "",
                                                "password" => ""
                                            ]
                                        ]
                                    ]
                                ],
                                'responses' => [
                                    200 => [
                                        'description' => 'give JWT token',
                                        'schema' => ['$ref' => "#/definitions/token"]
                                    ],
                                    401 => ['description' => 'Bad credentials']
                                ]
                            ]
                        ],
                ],
            'definitions' => [
                'token' => [
                    'type' => 'object',
                    'description' => "",
                    'properties' => [
                        'token' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ]
        ];
        $officialDocumentation = $this->normalizerDeferred->normalize($object, $format, $context);
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