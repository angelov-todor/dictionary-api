<?php
declare(strict_types=1);

namespace AppBundle\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Word;
use ApiPlatform\Core\Bridge\NelmioApiDoc;

class RhymeformAction
{
    /**
     * @Route(
     *     name="rhymeform",
     *     path="/words/{id}/rhymeform",
     *     defaults={"_api_resource_class"=Word::class, "_api_item_operation_name"="rhymeform"}
     * )
     * @Method("GET")
     * @return mixed
     */
    public function __invoke($data)
    {
        /* @var $data Word */

        $rhymeform = \AppBundle\Services\Rhymeform::findRhymeform($data->getName());
        return new \AppBundle\Entity\Rhymeform($rhymeform, $data);
    }
}