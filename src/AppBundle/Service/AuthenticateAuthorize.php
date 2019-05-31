<?php
/**
 *  AuthenticateAuthorize Service to handle Authentication and Authorization related tasks
 *
 *  @category Service
 *  @author <jayraja@mindfiresolutions.com>
 */

namespace AppBundle\Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use AppBundle\Entity\User;
use AppBundle\Security\UserToken;
use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class AuthenticateAuthorize
 * @package AppBundle\Service
 */
class AuthenticateAuthorize extends BaseService
{
    /**
     *  Function to authenticate Request Header.
     *
     *  @param Request $request
     *
     *  @return array
     */
    public function authenticateApiRequest(Request $request)
    {
        $authenticateResult['status'] = false;
        try {
            // Validating Content-Type in Request.
            $contentType = $request->headers->get('Content-Type');
            if ($request->getMethod() === Request::METHOD_POST
                && 'application/json' !== $contentType
            ) {
                throw new UnauthorizedHttpException(null, ErrorConstants::INVALID_CONTENT_TYPE);
            }

            // Checking Authorization Key for validating Token.
            $authorizationParts = explode(" ", $request->headers->get('apiKey'));
            if (
                    count($authorizationParts) !== 2 || 'Eat24' !== $authorizationParts[0]
                ||  empty(trim($authorizationParts[1]))
            ) {
                throw new UnauthorizedHttpException(null, ErrorConstants::INVALID_AUTHENTICATION);
            }

            // Parsing String Token to JWT Token Object.
            $token = (new Parser())->parse((string) trim($authorizationParts[1]));
            $signer = new Sha256();

            // Checking If Token passed in API Request Header is valid OR not.
            if (!$token->verify($signer, base64_decode($this->serviceContainer->getParameter('api_secret')))) {
                throw new UnauthorizedHttpException(null, ErrorConstants::INVALID_AUTH_TOKEN);
            }

            // Checking if Token is Expired.
            if ($token->isExpired()) {
                throw new UnauthorizedHttpException(null, ErrorConstants::TOKEN_EXPIRED);
            }

            // Checking That access_token must be used in API Calls.
            if (!$token->hasClaim('grant_type') ||  !$token->hasClaim('emailId')
                ||  GeneralConstants::ACCESS_TOKEN_GRANT !== $token->getClaim('grant_type')
            ) {
                throw new UnauthorizedHttpException(null, ErrorConstants::INVALID_AUTH_TOKEN);
            }

            $authenticateResult['message']['emailId'] = $token->getClaim('emailId');
            $tokenStorage = $this->serviceContainer->get('security.token_storage');

            // Creating Token and setting it in Token Storage for Current Request after User Authentication.
            $token = new UserToken($this->getUser($authenticateResult['message']['emailId']));
            $tokenStorage->setToken($token);

            $authenticateResult['status'] = true;
        } catch (\InvalidArgumentException $ex) {
            throw new UnauthorizedHttpException(null, ErrorConstants::INVALID_AUTH_TOKEN);
        } catch (UnauthorizedHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error('Authentication could not be complete due to Error : '.
                $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $authenticateResult;
    }

        /**
         *  Function to return User Object from email input.
         *
         *  @param string $email
         *  @param string $password (default = null)
         *
         *  @return User $user
         */
        public function getUser($email, $password = null)
        {
            $userManager = $this->serviceContainer->get('fos_user.user_manager');

            $params = ['email' => $email];

            // Checking if password is set then adding the password to the params.
            if (!empty($password)) {
                $params['password'] = $password;
            }

            return $userManager->findUserBy($params);
        }

    /**
     *  Function to process OAuth Request and
     *  return Access and Refresh Token.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function processOAuthRequest($requestContent)
    {
        $processingResult['status'] = false;
        try {
            $credentials = $requestContent['credentials'];
            // check user's username and password
            $validationResult = $this->validateUserCredentials($credentials);

            // Fetching returned User object on Success Case.
            $user = $validationResult['message']['user'];

            // create jwt access and refresh token
            $accessTokenResult = $this->createJWTForUser(
                $user,
                $this->serviceContainer->getParameter('api_access_token_expiry'),
                GeneralConstants::ACCESS_TOKEN_GRANT
            );

            $refreshTokenResult = $this->createJWTForUser(
                $user,
                $this->serviceContainer->getParameter('api_refresh_token_expiry'),
                GeneralConstants::REFRESH_TOKEN_GRANT
            );

            // Creating Response Array to be returned.
            $response = [
                'accessToken' => $accessTokenResult['message']['token'],
                'refreshToken' => $refreshTokenResult['message']['token'],
            ];
            $processingResult['message']['response'] = $response;
            $processingResult['status'] = true;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error('OAuth Request could not be processed due to Error : '.
                $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processingResult;
    }

    /**
     *  Function to Process OAuth refresh token request and return Processing response.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function processOAuthRefreshRequest($requestContent)
    {
        $processResult['status'] = false;
        try {
            $refreshToken = $requestContent['refreshToken'];
            // Parsing String Token to JWT Token Object.
            $token = (new Parser())->parse((string) $refreshToken);
            $signer = new Sha256();
            // Checking If Token passed in API Request Header is valid OR not.
            if (!$token->verify($signer, base64_decode($this->serviceContainer->getParameter('api_secret')))) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_REFRESH_TOKEN);
            }

            // Checking if Token is Expired.
            if ($token->isExpired()) {
                throw new UnprocessableEntityHttpException(ErrorConstants::EXPIRED_REFRESH_TOKEN);
            }

            // Checking That Refresh token passed must have refresh_token grant_type
            if (GeneralConstants::REFRESH_TOKEN_GRANT !== $token->getClaim('grant_type')) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_REFRESH_TOKEN);
            }

            // Creating Access Token for User
            $accessTokenResult = $this->createJWTForUser(
                $this->getUser($token->getClaim('emailId')),
                $this->serviceContainer->getParameter('api_access_token_expiry'),
                GeneralConstants::ACCESS_TOKEN_GRANT
            );

            $processResult['message']['response'] = [
                'accessToken' => $accessTokenResult['message']['token']
            ];
            $processResult['status'] = true;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error('OAuth Refresh Request could not be processed due to Error : '.
                $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     *  Function to Validate User and create Response Array.
     *
     *  @param array $credentials
     *
     *  @return array
     */
    public function validateUserCredentials($credentials)
    {
        $validateResult['status'] = false;
        try {
            $user = $this->getUser($credentials['emailId']);
            // checking if email is valid or not.
            if (empty($user)) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_CRED);
            }
            // fetch encoder service to encode password
            $encoder = $this->serviceContainer->get('security.encoder_factory')->getEncoder($user);

            if (!$user->isEnabled()) {
                throw new UnprocessableEntityHttpException(ErrorConstants::DISABLEDUSER);
            }

            // Checking if Password Provided is right
            if ($user->getPassword() !== $encoder->encodePassword($credentials['password'], $user->getSalt())) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_CRED);
            }

            $validateResult['message']['user'] = $user;
            $validateResult['status'] = true;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error('User credentials validation failed due to Error : '. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    /**
     *  Function to create and JWT for User.
     *
     *  @param User $user
     *  @param integer $expiry (default = 120)(for 2 Minutes(120 Seconds))
     *  @param string $grantType (default = GeneralConstants::ACCESS_TOKEN_GRANT)
     *
     *  @return array
     */
    public function createJWTForUser($user, $expiry = 120, $grantType = GeneralConstants::ACCESS_TOKEN_GRANT)
    {
        $createJWTResult['status'] = false;
        try {
            $signer = new Sha256();
            $token = (new Builder())
                // Configures the time that the token was issue (iat claim)
                ->setIssuedAt(time())
                // Configures the time that the token can be used (nbf claim)
                ->setNotBefore(time() + 5)
                // Configures the expiration time ($expiry for this case) of the token (exp claim)
                ->setExpiration(time() + $expiry)
                //  Creating Claim email
                ->set('emailId', $user->getEmail())
                // Grant Type (Either access_token OR refresh_token)
                ->set('grant_type', $grantType)
                // Creating Signature.
                ->sign($signer, base64_decode($this->serviceContainer->getParameter('api_secret')))
                // Retrieves Generated Token Object
                ->getToken()
                // Converts Token into encoded String.
                ->__toString()
            ;

            $createJWTResult['message']['token'] = $token;
            $createJWTResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error('Creating JWT Failed Due to Error : ' .
                $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $createJWTResult;
    }
}
