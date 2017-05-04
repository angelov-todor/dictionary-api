<?php
declare(strict_types=1);

namespace AppBundle\Action\Dictionary;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Dictionary\Word;

class SyllablesAction
{
    /**
     * @Route(
     *     name="rhymeform",
     *     path="/words/{id}/syllables",
     *     defaults={"_api_resource_class"=Word::class, "_api_item_operation_name"="syllables"}
     * )
     * @Method("GET")
     * @return mixed
     */
    public function __invoke($data)
    {
        /* @var $data Word */
        $service = new \AppBundle\Services\Syllables();
        $syllables = $service->processWord($data->getName());
        return new JsonResponse([
            'syllables' => $syllables
        ]);
    }
}