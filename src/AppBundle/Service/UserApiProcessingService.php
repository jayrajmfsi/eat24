<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use AppBundle\Entity\Utils\Point;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserApiProcessingService extends BaseService
{
    /**
     *  Function to process User list API request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function processUserListRequest($requestContent)
    {
        $processResult['status'] = false;
        try {
            $filter = !empty($requestContent['filter']) ? $requestContent['filter'] : null;
            $sort = !empty($requestContent['sort']) ? $requestContent['sort'] : null;

            $userRepo = $this->entityManager->getRepository('AppBundle:User');
            $users = $userRepo->fetchUserListData($filter, $sort, $requestContent['pagination']);
            $total = $userRepo->countUserRecords($filter, $sort);

            // Iterating over users and creating the users array to be returned in response.
            foreach ($users as $key => $user) {
                $users[$key]['createdDateTime'] = $user['createdDateTime']->format('Y-m:d H:i:s');
                $users[$key]['lastUpdateDateTime'] = $user['lastUpdateDateTime']->format('Y-m:d H:i:s');
            }

            $processResult['message']['response'] = [
                'users' => $users,
                'count' => $total
            ];
            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to process User List export request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function processUserListExportRequest($requestContent)
    {
        $processResult['status'] = false;
        try {
            $filter = !empty($requestContent['filter']) ? $requestContent['filter'] : null;
            $sort = !empty($requestContent['sort']) ? $requestContent['sort'] : null;

            $userRepo = $this->entityManager->getRepository('AppBundle:User');
            $total = $userRepo->countUserRecords($filter, $sort);

            $batchSize = (int)$this->serviceContainer->getParameter('resource_export_batch_size');
            $batches = ceil($total / $batchSize);

            // Adding the file headers to File Content.
            $fileContent[] = implode(',', ['id', 'username', 'email', 'roles', 'enabled',
                'created date time', 'last update date time']);
            $exportFileName = 'export_user_list_'.
                date('Y_m_d_H_i_s', strtotime('now')) .'.csv';
            $mmtTimeZone = new \DateTimeZone('Asia/Rangoon');
            $utilService = $this->serviceContainer->get('b2b_eload.utils');
            $exportFileDir = $this->serviceContainer->getParameter('resource_export_file_dir');

            // Writing the headers Content to File.
            $file = $utilService->writeContentToFile($exportFileDir, $exportFileName,
                implode(PHP_EOL, $fileContent).PHP_EOL)
            ;

            // Fetching Data in Batch Size and writing to File to be exported.
            for ($fetchCount = 1; $fetchCount <= $batches ; $fetchCount++) {
                // Assigning the File Content to Empty Array.
                $fileContent = [];

                $users = $userRepo->fetchUserListData($filter, $sort, [
                    'page' => $fetchCount,
                    'limit' => $batchSize,
                ]);

                // Iterating over users and creating the users array to be returned in response.
                foreach ($users as $key => $user) {
                    $users[$key]['enabled'] = !empty($user['enabled']) ? 'Yes' : 'No';
                    $users[$key]['roles'] = implode(', ', $user['roles']);
                    $users[$key]['createdDateTime'] = $user['createdDateTime']->setTimeZone($mmtTimeZone)
                        ->format('Y-m:d H:i:s');
                    $users[$key]['lastUpdateDateTime'] = $user['lastUpdateDateTime']->setTimeZone($mmtTimeZone)
                        ->format('Y-m:d H:i:s');

                    $fileContent[] = implode(',', $users[$key]);
                }

                // Appending the Content to file.
                $file = $utilService->writeContentToFile($exportFileDir, $exportFileName,
                    implode(PHP_EOL, $fileContent).PHP_EOL, true)
                ;
            }

            $processResult['message']['response'] = $utilService->createFileStreamedResponse($file);
            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to process Create/Update User request.
     *
     *  @param array $requestContent
     *  @param User $user (default = null)
     *
     *  @return array
     *  @throws \Exception
     */
    public function processCreateUpdateUserRequest($requestContent, $user = null)
    {
        $processResult['status'] =  false;
        try {
            $data = [];

            // setting the available values to update in $data array.
            if(!empty($requestContent['UserRequest']['username'])) {
                $data['username'] = $requestContent['UserRequest']['username'];
            }

            if (!empty($requestContent['UserRequest']['newPassword'])
                || !empty($requestContent['UserRequest']['password'])
            ) {
                $data['password'] = isset($requestContent['UserRequest']['newPassword'])
                    ? $requestContent['UserRequest']['newPassword']
                    : $requestContent['UserRequest']['password']
                ;
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
            if(!empty($data)) {
                $processResult['message']['response'] = [
                    'user' => $this->createUpdateUserObject($data, $user)
                ];
            }

            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to Create/Update Transaction Object.
     *
     *  @param array $data
     *  @param User $user
     *
     *  @return User
     *  @throws \Exception
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
                ->updateUser($user)
            ;
        }

        return $user;
    }

    /**
     *  Function to process the GET request for user's Profile.
     *
     *  @param string $email
     *  @return array
     */
    public function processGetUserProfileRequest($email)
    {
        $processResult['status'] = false;
        try {
            /** @var User $userDetails */
            $userDetails = $this->serviceContainer->get('fos_user.user_manager')
                ->findUserByEmail($email)
            ;
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
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to process the Change Password request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function processChangePasswordRequest($requestContent)
    {
        $processResult['status'] = false;
        try {

            $this->serviceContainer
                ->get('fos_user.util.user_manipulator')
                ->changePassword(
                    $this->getCurrentUser()->getUsername(),
                    $requestContent['UserPasswordRequest']['password']
                )
            ;

            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
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
                    ->createObjectFromArray($data, Address::class, $address)
                ;
            }

            if (!empty($requestContent['latitude']) && !empty($requestContent['longitude'])) {
                $address->setGeoPoint(new Point($requestContent['latitude'], $requestContent['longitude']));
            }
            $address->setAddressType(Address::CUSTOMER_ADDRESS);
            $address->setCustomerId($user->getId());
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $processResult['addressCode'] = $address->getToken();
            $processResult['status'] = true;

        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }
}