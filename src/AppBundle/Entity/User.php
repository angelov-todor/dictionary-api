<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiResource(
 *      itemOperations={
 *          "get"={"method"="GET"},
 *          "put"={"method"="PUT"},
 *          "delete"={"method"="DELETE"},
 *          "authenticate"={
 *              "route_name"="authenticate",
 *               "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "username",
 *                          "in" = "body",
 *                          "required" = "true",
 *                          "type" = "string"
 *                      },
 *                      {
 *                          "name" = "password",
 *                          "in" = "body",
 *                          "required" = "true",
 *                          "type" = "string"
 *                      }
 *                  },
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "JWT authentication token",
 *                          "schema" =  {
 *                              "type" = "object",
 *                              "required" = {
 *                                  "token"
 *                              },
 *                              "properties" = {
 *                                   "token" = {
 *                                      "type" = "string"
 *                                   }
 *                              }
 *                          }
 *                      },
 *                      "400" = {
 *                          "description" = "Invalid input"
 *                      },
 *                      "401" = {
 *                          "description" = "Bad credentials"
 *                      }
 *                  },
 *                  "summary" = "Acquire JWT Token",
 *                  "consumes" = {
 *                      "application/json"
 *                   },
 *                  "produces" = {
 *                      "application/json"
 *                   }
 *              }
 *          }
 *      },
 *      attributes={
 *          "normalization_context"={"groups"={"user", "user-read"}},
 *          "denormalization_context"={"groups"={"user", "user-write"}}
 *      }
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Groups({"user"})
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user"})
     */
    protected $fullname;

    /**
     * @Groups({"user-write"})
     */
    protected $plainPassword;

    /**
     * @Groups({"user"})
     */
    protected $username;

    /**
     * @param $fullname
     * @return $this
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    public function isUser(UserInterface $user = null)
    {
        return $user instanceof self && $user->id === $this->id;
    }
}