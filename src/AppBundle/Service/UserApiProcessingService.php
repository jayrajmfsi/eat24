<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use AppBundle\Entity\Utils\Point;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserApiProcessingService extends BaseService
{
    /**
     *  Function to process Create/Update User request.
     *
     * @param array $requestContent
     * @param User $user (default = null)
     *
     * @return array
     * @throws \Exception
     */
    public function processCreateUpdateUserRequest($requestContent, $user = null)
    {
        $processResult['status'] = false;
        try {
            $data = [];

            // setting the available values to update in $data array.
            if (!empty($requestContent['UserRequest']['username'])) {
                $data['username'] = $requestContent['UserRequest']['username'];
            }

            if (!empty($requestContent['UserRequest']['newPassword'])
                || !empty($requestContent['UserRequest']['password'])
            ) {
                $data['password'] = isset($requestContent['UserRequest']['newPassword'])
                    ? $requestContent['UserRequest']['newPassword']
                    : $requestContent['UserRequest']['password'];
            }

            // Setting the email in data if User is being Created.
            if (empty($user)) {
                $data['email'] = $requestContent['UserRequest']['emailId'];
            }
            // Setting the phone number in data if User is being Created.
            if (!empty($requestContent['UserRequest']['phoneNumber'])) {
                $data['contactNumber'] = $requestContent['UserRequest']['phoneNumber'];
            }

            // if array is not a empty then update the details.
            if (!empty($data)) {
                $processResult['message']['response'] = [
                    'user' => $this->createUpdateUserObject($data, $user)
                ];
            }

            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' Function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to Create/Update Transaction Object.
     *
     * @param array $data
     * @param User $user
     *
     * @return User
     * @throws \Exception
     */
    public function createUpdateUserObject($data, $user = null)
    {
        $userManipulator = $this->serviceContainer->get('fos_user.util.user_manipulator');

        if (empty($user)) {
            // Creating User.
            /** @var User $user */
            $user = $userManipulator
                ->create(
                    $data['username'], $data['password'],
                    $data['email'], true, false
                );
            if (!empty($data['contactNumber'])) {
                $user->setContactNumber((int)$data['contactNumber']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

        } else {
            $utils = $this->serviceContainer->get('eat24.utils');
            // Changing password if password is received in data array.
            if (isset($data['password'])) {
                $userManipulator->changePassword($user->getUsername(), $data['password']);
                unset($data['password']);
            }

            // Filling $data attributes into User Object
            $user = $utils->createObjectFromArray($data, User::class, $user);
            $this->serviceContainer
                ->get('fos_user.user_manager')
                ->updateUser($user);
        }

        return $user;
    }

    /**
     *  Function to process the GET request for user's Profile.
     *
     * @param string $email
     * @return array
     */
    public function processGetUserProfileRequest($email)
    {
        $processResult['status'] = false;
        try {
            /** @var User $userDetails */
            $userDetails = $this->serviceContainer->get('fos_user.user_manager')
                ->findUserByEmail($email);
            if (empty($userDetails)) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_EMAIL);
            }
            // Creating ProfileResponse Array.
            $profileResponse['username'] = $userDetails->getUserName();
            $profileResponse['phoneNumber'] = $userDetails->getContactNumber();

            $processResult['message']['response'] = [
                'profileDetails' => $profileResponse,
            ];
            $processResult['status'] = true;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' Function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     * @param $requestContent
     * @param User $user
     * @param mixed $address
     * @return mixed
     */
    public function processUpdateAddressRequest($requestContent, $user, $address = null)
    {
        $processResult['status'] = false;
        if (empty($address)) {
            $address = new Address();
        }
        try {
            if (!empty($requestContent['location'])) {
                $data['mapLocation'] = $requestContent['location'];
            }

            if (!empty($requestContent['nickName'])) {
                $data['nickName'] = $requestContent['nickName'];
            }

            if (!empty($requestContent['completeAddress'])) {
                $data['completeAddress'] = $requestContent['completeAddress'];
            }

            // if array is not a empty then update the details.
            if (!empty($data)) {
                /** @var Address $address */
                $address = $this->serviceContainer->get('eat24.utils')
                    ->createObjectFromArray($data, Address::class, $address);
            }

            if (!empty($requestContent['latitude']) && !empty($requestContent['longitude'])) {
                $address->setGeoPoint(new Point($requestContent['latitude'], $requestContent['longitude']));
            }
            $address->setToken($this->generateNewTransactionId($address->getId()));
            $address->setAddressType(Address::CUSTOMER_ADDRESS);
            $address->setCustomerId($user->getId());
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $processResult['addressCode'] = $address->getToken();
            $processResult['status'] = true;

        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' Function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    public function processDeleteAddressRequest($content)
    {
        try {
            $userId = $this->getCurrentUser()->getId();
            $address = $this->entityManager->getRepository('AppBundle:Address')
                ->getAddress($userId, $content['addressCode']);
            if ($address) {
                $this->entityManager->remove($address);
                $this->entityManager->flush();
            } else {
                throw new BadRequestHttpException(ErrorConstants::INVALID_ADDRESS_CODE);
            }
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' Function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }
    }

    /**
     * return the list of addresses for a particular user
     * @return array $processResult
     */
    public function processListAddressRequest()
    {
        $processResult['status'] = false;
        try {

            $addressResult = $this->entityManager->getRepository('AppBundle:Address')
                ->listUserAddress($this->getCurrentUser()->getId())
            ;
            $addressResult = empty($addressResult) ? [] : $addressResult;
            $processResult['status'] = true;
            $processResult['message']['response'] = $addressResult;

        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    public function processCheckDeliveryLocationRequest($restaurantId, $content)
    {
        $processResult['status'] = false;
        try {
            $address = $this->entityManager->getRepository('AppBundle:Address')
                ->checkDeliveryLocation(
                    $content['longitude'],
                    $content['latitude'],
                    $restaurantId,
                    $this->serviceContainer->getParameter('restaurant_range')
                )
            ;
            if ($address) {
                $processResult['status'] = true;
            }
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }
}