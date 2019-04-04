<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
     *  Function to validate the Users list/Export API request.
     *
     *  @param array $requestContent
     *  @param bool $isExport (default = false)
     *
     *  @return array
     */
    public function validateUsersListExportRequest($requestContent, $isExport = false)
    {
        $validateResult['status'] = false;
        try {
            $content = [];
            // Validating the filters Key Values.
            if (
                    !empty($requestContent['filter']['username'])
                &&  strlen($requestContent['filter']['username']) <= 100
            ) {
                $content['filter']['username'] = $requestContent['filter']['username'];
            }

            if (
                    !empty($requestContent['filter']['email'])
                &&  strlen($requestContent['filter']['email']) <= 100
            ) {
                $content['filter']['email'] = $requestContent['filter']['email'];
            }

            if (isset($requestContent['filter']['isEnabled'])) {
                $content['filter']['isEnabled'] = is_bool($requestContent['filter']['isEnabled'])
                    ? $requestContent['filter']['isEnabled']
                    : true
                ;
            }

            // Validating the Created Date time.
            if (
                    !empty($requestContent['filter']['createdDate']['from'])
                &&  !empty($createdFromDate = \DateTime::createFromFormat('Y-m-d',
                    $requestContent['filter']['createdDate']['from']))
            ) {
                $content['filter']['createdDate']['from'] = $createdFromDate;
            }

            if (
                    !empty($requestContent['filter']['createdDate']['to'])
                &&  !empty($createdToDate = \DateTime::createFromFormat('Y-m-d',
                    $requestContent['filter']['createdDate']['to']))
            ) {
                $content['filter']['createdDate']['to'] = $createdToDate;
            }

            if (
                    empty($content['filter']['createdDate']['from'])
                &&  !empty($content['filter']['createdDate']['to'])
            ) {
                $content['filter']['createdDate']['from'] = $content['filter']['createdDateTime']['to'];
            }

            if (
                    !empty($content['filter']['createdDate']['from'])
                &&  empty($content['filter']['createdDate']['to'])
            ) {
                $content['filter']['createdDate']['to'] = $content['filter']['createdDateTime']['from'];
            }

            // Validating the Sort attributes.
            if (
                    !empty($requestContent['sort'])
                &&  2 === count($requestContent['sort'])
                &&  isset(User::$allowedSortingAttributesMap[$requestContent['sort'][0]])
            ) {
                $content['sort'][] = $requestContent['sort'][0];
                $content['sort'][] = ('ASC' === $requestContent['sort'][1] || 'DESC' === $requestContent['sort'][1])
                    ? $requestContent['sort'][1]
                    : 'ASC'
                ;
            }

            // Checking if isExport is not set.
            if (!$isExport) {
                // Validating the pagination parameters.
                $content['pagination'] = $this->serviceContainer
                    ->get('eat24.utils')
                    ->validatePaginationArray($requestContent['pagination'])
                ;
            }

            if (!empty($content)) {
                $validateResult['message']['response'] = [
                    'content' => $content
                ];
            }
            $validateResult['status'] = true;
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
                throw new UnprocessableEntityHttpException(ErrorConstants::USERNAME_EXISTS);
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
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    public function validateUpdateUserRequest($requestContent, $email)
    {
        $validateResult['status'] = false;
        try {
            $userManager = $this->serviceContainer->get('fos_user.user_manager');

            // Checking that all the required keys should be present.
            if (empty($requestContent['UserRequest'])) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }

            $user = $userManager->findUserBy(['email' => $email]);
            if (empty($user)) {

                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_EMAIL);
            }

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
                throw new BadRequestHttpException(ErrorConstants::INVALID_NEWPASSFORMAT);
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
     *  Function to validate the get User's Profile API.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function validateFetchUserProfileRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            // Checking if username is not present in the request
            // then marking validation status true.
            if (empty($requestContent['UserRequest']['username'])) {
                $validateResult['status'] = true;

                return $validateResult;
            }

            // Validating the Username present in request
            $user = $this->serviceContainer
                ->get('eat24.authenticate_authorize_service')
                ->getUser($requestContent['UserRequest']['emailId'])
            ;

            // Checking if the username provided is valid or Not.
            if (empty($user)) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_USERNAME);
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
            throw new BadRequestHttpException(ErrorConstants::INVALID_USERNAME);
        }

        $previousUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserByUsername($userName)
        ;

        // Checking if UserName is already taken by someone.
        if (!empty($previousUser) && (empty($user) || $user->getId() !== $previousUser->getId())) {
            throw new UnprocessableEntityHttpException(ErrorConstants::USERNAME_EXISTS);
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
            throw new BadRequestHttpException(ErrorConstants::INVALID_PHONE_NUMBER);
        }

        $previousUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserBy(['contactNumber' => $phoneNumber])
        ;

        // Checking if phoneNumber is already taken by someone.
        if (!empty($previousUser) && (empty($user) || $user->getId() !== $previousUser->getId())) {
            throw new UnprocessableEntityHttpException(ErrorConstants::PHONE_NUMBER_EXISTS);
        }
    }

    /**
     *  Function to validate Email While Updating OR Creating Object.
     *
     *  @param string $email
     *  @param User $user (default = null)
     *
     *  @return void
     */
    public function validateEmail($email, $user = null)
    {
        // Checking if the email is valid.
        if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_EMAIL);
        }

        $emailUser = $this->serviceContainer
            ->get('fos_user.user_manager')
            ->findUserByEmail($email)
        ;

        // Checking if Email is already taken by someone.
        if (!empty($emailUser) && (empty($user) || $user->getId() !== $emailUser->getId())) {
            throw new UnprocessableEntityHttpException(ErrorConstants::EMAIL_EXISTS);
        }
    }
}