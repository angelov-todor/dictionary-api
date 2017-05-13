<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Image;

class SearchAction
{
    /**
     * @var string
     */
    protected $cx = '009818310687169508712:jsxa4kza75m';
    /**
     * @var string
     */
    protected $key = 'AIzaSyAKX55B8g6DBfDfkJ-nzhqj-hDUUKcLqLc';
    /**
     * @var string
     */
    protected $uri = 'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s&searchType=image&start=%s';

    /**
     * @Route(
     *     name="image-search",
     *     path="/images.search",
     *     defaults={"_api_resource_class"=Image::class, "_api_collection_operation_name "="search"}
     * )
     * @Method("GET")
     * @return mixed
     */
    public function __invoke($data)
    {
        return new JsonResponse();
    }
}