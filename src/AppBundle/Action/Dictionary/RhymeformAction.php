<?php
declare(strict_types=1);

namespace AppBundle\Action\Dictionary;

use AppBundle\Services\Rhymeform;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Dictionary\Word;

class RhymeformAction
{
    /**
     * @Route(
     *     name="rhymeform",
     *     path="/words/{id}/rhymeform",
     *     defaults={"_api_resource_class"=Word::class, "_api_item_operation_name"="rhymeform"}
     * )
     * @Method("GET")
     * @param mixed $data
     * @return mixed
     */
    public function __invoke($data)
    {
        /* @var $data Word */
        $rhymeform = Rhymeform::findRhymeform($data->getName());
        return new JsonResponse([
            'rhymeform' => $rhymeform
        ]);
    }
}