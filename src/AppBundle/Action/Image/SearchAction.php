<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class SearchAction
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
     *     name="images_search",
     *     path="/images-search"
     * )
     * @Method("GET")
     *
     * @param Request $request
     * @return JsonResponse|Response|ResponseHeaderBag
     */
    public function imageSearchAction(Request $request)
    {
        $term = $request->get('term');
        $page = $request->get('page', 1);
        $start = (($page - 1) * 10) + 1;
        $url = sprintf($this->uri, $this->key, $this->cx, $term, $start);
        $response = file_get_contents($url);
        return new JsonResponse(json_decode($response));
    }
}