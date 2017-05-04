<?php
declare(strict_types=1);

namespace AppBundle\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class UserAction
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
     * @Route(
     *     name="authenticate",
     *     path="/authenticate"
     * )
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse|Response|ResponseHeaderBag
     */
    public function authenticateAction(Request $request)
    {
        try {

            $loginRequest = json_decode($request->getContent(false));

            $username = $loginRequest->username;
            $password = $loginRequest->password;

            if (is_null($username) || is_null($password)) {
                return new ResponseHeaderBag(json_encode([
                    'code' => Response::HTTP_UNAUTHORIZED,
                    "message" => 'Please verify all your inputs.'
                ]), Response::HTTP_UNAUTHORIZED, array(
                    'Content-type' => 'application/json'
                ));
            }

            $user_manager = $this->container->get('fos_user.user_manager');
            $factory = $this->container->get('security.encoder_factory');

            $user = $user_manager->findUserByUsername($username);
            if (!$user) {
                $response = new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
                $response->setData([
                    "code" => Response::HTTP_UNAUTHORIZED,
                    "message" => "Bad credentials"
                ]);

                return $response;
            }

            $encoder = $factory->getEncoder($user);
            $salt = $user->getSalt();

            if ($encoder->isPasswordValid($user->getPassword(), $password, $salt)) {

                $response = $this->getUserTokenResponse($user);
            } else {
                $response = new Response(json_encode([
                    'code' => Response::HTTP_UNAUTHORIZED,
                    "message" => 'Username or Password not valid.'
                ]), Response::HTTP_UNAUTHORIZED, array(
                    'Content-type' => 'application/json'
                ));
            }

            return $response;
        } catch (\Throwable $t) {
            return new Response($t->getMessage());
        }
    }

    protected function getUserTokenResponse($user): JsonResponse
    {
        /** @var JWTManager $jwtManager */
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');

        $jwt = $jwtManager->create($user);
        $response = new JsonResponse();
        $event = new AuthenticationSuccessEvent(array(
            'token' => $jwt
        ), $user, $response);
        $dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;
    }
}