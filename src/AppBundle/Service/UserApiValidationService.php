<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserApiValidationService extends BaseService
{
    /**
     *  Function to Validate the request content of
     *  POST /admin/user/oauth
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function validateOAuthRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {

            // Checking if both fields of credentials are provided.
            if (
                    empty($requestContent['credentials']['emailId'])
                ||  empty($requestContent['credentials']['password'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_CRED);
            }

            // Checking the length of both fields of credentials.
            if (
                    strlen($requestContent['credentials']['emailId']) > 180
                ||  strlen($requestContent['credentials']['password']) > 180
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_CRED);
            }

            $validateResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    /**
     *  Function to validate the Create User request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function validateCreateUserRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            $userManager = $this->serviceContainer->get('fos_user.user_manager');

            // Checking that all the required keys should be present.
            if (
                    empty($requestContent['UserRequest'])
                ||  empty($requestContent['UserRequest']['username'])
                ||  empty($requestContent['UserRequest']['emailId'])
                ||  empty($requestContent['UserRequest']['phoneNumber'])
                ||  empty($requestContent['UserRequest']['password'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }

            $user = $userManager->findUserByUsername($requestContent['UserRequest']['username']);

            // Checking that both username and email if provided should be unique.
            if (!empty($user)) {
                throw new ConflictHttpException(ErrorConstants::USERNAME_EXISTS);
            }

            // validate username
            $this->validateUserName($requestContent['UserRequest']['username']);
            // Validating Email
            $this->validateEmail($requestContent['UserRequest']['emailId']);

            $this->validatePassword($requestContent['UserRequest']['password']);
            $this->validatePhoneNumber($requestContent['UserRequest']['phoneNumber'], $user);

            $validateResult['status'] = true;
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (ConflictHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    public function validateUpdateUserRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            // Checking that all the required keys should be present.
            if (empty($requestContent['UserRequest'])) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }

            $user = $this->getCurrentUser();

            // validate username
            if (!empty($requestContent['UserRequest']['username'])) {
                $this->validateUserName($requestContent['UserRequest']['username'], $user);
            }

            if (!empty($requestContent['UserRequest']['phoneNumber'])) {
                $this->validatePhoneNumber($requestContent['UserRequest']['phoneNumber'], $user);
            }

            if (!empty($requestContent['UserRequest']['oldPassword'])
            ) {
                $this->validateChangePasswordRequest(
                    $requestContent['UserRequest']['oldPassword'],
                    !empty($requestContent['UserRequest']['newPassword'])
                        ? $requestContent['UserRequest']['newPassword'] : null
                );
            }

            $validateResult['message']['response'] = [
                'user' => $user,
            ];

            $validateResult['status'] = true;
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (ConflictHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    /**
     *  Function to Validate the oauth refresh API request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function validateOAuthRefreshRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            // Checking that all the required Fields in request should be present.
            if (
                    empty($requestContent)
                ||  empty($requestContent['refreshToken'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REFRESH_TOKEN);
            }

            // checking the length of the refreshToken.
            if (strlen($requestContent['refreshToken']) > 1000) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_REFRESH_TOKEN);
            }

            $validateResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }


    public function validatePassword($password)
    {
        // Checking the format of Password.
        if (strlen($password) > 100) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_NEWPASSFORMAT);
        }
    }

    /**
     *  Function to validate the Change Password request content.
     *
     *  @param string $oldPassword
     *  @param string $newPassword
     *
     *  @return array
     */
    public function validateChangePasswordRequest($oldPassword, $newPassword)
    {
        $validateResult['status'] = false;
        try {
            // Checking the format of new Old Password.
            if (empty($newPassword) || strlen($newPassword) > 100) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_NEWPASSFORMAT);
            }

            $user = $this->serviceContainer
                ->get('fos_user.user_manager')
                ->findUserBy([
                    'username' => $this->getCurrentUser()->getUsername(),
                    'password' => $this->serviceContainer->get('security.encoder_factory')
                        ->getEncoder($this->getCurrentUser())
                        ->encodePassword(
                            $oldPassword,
                            $this->getCurrentUser()->getSalt()
                        ),
                ])
            ;
            // Checking
            // if the Old Password provided is valid or not.
            if (empty($user)) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_OLDPASS);
            }

            $validateResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    /**
     *  Function to validate UserName While Updating OR Creating Object.
     *
     *  @param string $userName
     *  @param User $user (default = null)
     *
     *  @return void
     */
    public function validateUserName($userName, $user = null)
    {
        // Checking if the username is valid.
        if (strlen($userName) > 50) {
            throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_USERNAME);
        }

        $previousUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserByUsername($userName)
        ;

        // Checking if UserName is already taken by someone.
        if (!empty($previousUser) && (empty($user) || $user->getId() !== $previousUser->getId())) {
            throw new ConflictHttpException( ErrorConstants::USERNAME_EXISTS);
        }
    }

    /**
     *  Function to validate Email While Updating OR Creating Object.
     *
     *  @param string $phoneNumber
     *  @param User $user (default = null)
     *
     *  @return void
     */
    public function validatePhoneNumber($phoneNumber, $user = null)
    {
        // Checking if the phone number is valid.
        if (strlen($phoneNumber) > 10) {
            throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_PHONE_NUMBER);
        }

        $previousUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserBy(['contactNumber' => $phoneNumber])
        ;

        // Checking if phoneNumber is already taken by someone.
        if (!empty($previousUser) && (empty($user) || $user->getId() !== $previousUser->getId())) {
            throw new HttpException(409, ErrorConstants::PHONE_NUMBER_EXISTS);
        }
    }

    /**
     *  Function to validate Email While Updating OR Creating Object.
     *
     *  @param string $email
     *  @param User $user (default = null)
     *  @return User
     *
     *  @return void
     */
    public function validateEmail($email, $user = null)
    {
        // Checking if the email is valid.
        if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_EMAIL_FORMAT);
        }

        $emailUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserByEmail($email)
        ;

        // Checking if Email is already taken by someone.
        if (!empty($emailUser) && (empty($user) || $user->getId() !== $emailUser->getId())) {
            throw new ConflictHttpException(ErrorConstants::EMAIL_EXISTS);
        }
        return $emailUser;
    }

    public function validateDeleteAddressRequest($requestContent)
    {
        $validatedResult['status'] = false;
        try {
            if (empty($requestContent['addressCode'])) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }

            $validatedResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (\Exception $exception) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $exception->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validatedResult;
    }

    public function validateAddUpdateAddressRequest($requestContent, $isUpdate = false)
    {
        $validateResult['status'] = false;
        try {
            $validateResult['user'] = $this->getCurrentUser();
            if ($isUpdate) {
                if (empty($requestContent['UserDeliveryAddressRequest'])
                    || empty($requestContent['UserDeliveryAddressRequest']['addressCode'])
                ) {
                    throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
                }

                $address = $this->serviceContainer->get('eat24.utils')
                    ->validateAddressCode(
                        $requestContent['UserDeliveryAddressRequest']['addressCode'],
                        ($validateResult['user'])->getId()
                    )
                ;
                $validateResult['address'] = $address;

                return $validateResult;

            } else {
                // checking for add address request
                if (empty($requestContent['UserDeliveryAddressRequest'])
                    ||  empty($requestContent['UserDeliveryAddressRequest']['location'])
                    || empty($requestContent['UserDeliveryAddressRequest']['longitude'])
                    || empty($requestContent['UserDeliveryAddressRequest']['latitude'])
                    || empty($requestContent['UserDeliveryAddressRequest']['completeAddress'])
                ) {
                    throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
                }

                // Checking the type of both fields of point.
                if (!(float)$requestContent['UserDeliveryAddressRequest']['latitude'] ||
                    !(float)$requestContent['UserDeliveryAddressRequest']['longitude']
                ) {
                    throw new BadRequestHttpException(ErrorConstants::INVALID_GEO_POINT);
                }
            }

            $validateResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    public function validateCheckDeliveryLocationRequest($requestContent)
    {
        $validatedResult['status'] = false;
        try {
            if (empty($requestContent['detectLocationRequest'])
                || empty($requestContent['detectLocationRequest']['longitude'])
                || empty($requestContent['detectLocationRequest']['latitude'])
                || empty($requestContent['detectLocationRequest']['restaurantCode'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }
            $restaurant = $this->serviceContainer->get('eat24.utils')
                ->validateRestaurantCode($requestContent['detectLocationRequest']['restaurantCode'])
            ;

            $validatedResult['response'] = $restaurant->getId();
            $validatedResult['status'] = true;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (\Exception $exception) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $exception->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }
        return $validatedResult;
    }

    public function validateCreateOrderRequest($requestContent)
    {
        $validatedResult['status'] = false;
       try {
           if (empty($requestContent['orderRequest'])
               || empty($requestContent['orderRequest']['orderItems'])
               || empty($requestContent['orderRequest']['restaurantCode'])
               || empty($requestContent['orderRequest']['addressCode'])
               || empty($requestContent['orderRequest']['totalPrice'])
               || !is_array($requestContent['orderRequest']['orderItems'])
           ) {
               throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
           }

           foreach ($requestContent['orderRequest']['orderItems'] as $orderItem) {
               $this->validateOrderItemsRequest($orderItem);
           }
           $restaurant = $this->serviceContainer->get('eat24.utils')
               ->validateRestaurantCode($requestContent['orderRequest']['restaurantCode'])
           ;
           $user = $this->getCurrentUser();
           $address = $this->serviceContainer->get('eat24.utils')
               ->validateAddressCode($requestContent['orderRequest']['addressCode'], $user->getId())
           ;
           $validatedResult['message']['response'] = [
               'user' => $user,
               'address' => $address,
               'restaurant' => $restaurant
           ];
           $validatedResult['status'] = true;

       }  catch (BadRequestHttpException $ex) {
           throw $ex;
       } catch (\Exception $exception) {
           $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $exception->getMessage());
           throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
       }

       return $validatedResult;
    }

    public function validateOrderItemsRequest($orderItem)
    {
        if (empty($orderItem['name'])
            || !(int)($orderItem['quantity'])
            || empty($orderItem['code'])
        ) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
        }
    }
}