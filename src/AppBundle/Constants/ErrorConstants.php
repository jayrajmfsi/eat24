<?php
/**
 *  Error Constants file for Storing Error Message codes and Message Text for Application.
 *
 *  @category Constants
 */

namespace AppBundle\Constants;

final class ErrorConstants
{
    const INTERNAL_ERR = 'INTERNALERR';
    const INVALID_CONTENT_TYPE = 'INVALIDCONTENTTYPE';
    const INVALID_CONTENT_LENGTH = 'INVALIDCONTENTLEN';
    const INVALID_REQ_DATA = 'INVALIDREQDATA';
    const RESOURCE_NOT_FOUND = 'NORESOURCEFOUND';
    const INVALID_AUTHENTICATION = 'INVALIDAUTHENTICATION';
    const INVALID_AUTHORIZATION = 'INVALIDAUTHORIZATION';
    const METHOD_NOT_ALLOWED = 'METHODNOTALLOWED';
    const REQ_TIME_OUT = 'REQTIMEOUT';
    const SERVICE_UNAVAIL = 'SERVICEUNAVAIL';
    const INVALID_ADDRESS_CODE = 'INVALIDADDRESSCODE';
    const INVALID_RESTAURANT_CODE = 'INVALIDRESTAURANTCODE';
    const GATEWAY_TIMEOUT = 'GATEWAYTIMEOUT';
    const INVALID_PHONE_NUMBER = 'INVALIDPHONENO';
    const INVALID_EMAIL_FORMAT = 'INVALIDEMAILFORMAT';
    const INVALID_USERNAME = 'INVALIDUSERNAME';
    const INVALID_CRED = 'INVALIDCRED';
    const INVALID_AUTH_TOKEN = 'INVALIDAUTHTOKEN';
    const TOKEN_EXPIRED = 'TOKENEXPIRED';
    const USERNAME_EXISTS = 'USERNAMEPREEXIST';
    const PHONE_NUMBER_EXISTS = 'PHONENUMBEREXIST';
    const EMAIL_EXISTS = 'EMAILPREEXISTS';
    const INVALID_GEO_POINT = 'INVALIDGEOPOINT';
    const INVALID_MENU_ITEM_CODE = 'INVALID_RESTAURANT_CODE';
    const INVALID_REFRESH_TOKEN = 'INVALIDREFRESHTOKEN';
    const EXPIRED_REFRESH_TOKEN = 'EXPIREDREFRESHTOKEN';
    const INVALID_OLD_PASS = 'INVALIDOLDPASS';
    const INVALID_NEW_PASS_FORMAT = 'INVALIDNEWPASSFORMAT';
    const DISABLEDUSER = 'DISABLEDUSER';
    const INVALID_CONFIRM_PASS = 'INVALIDCONFIRMPASSWORD';

    public static $errorCodeMap = [
        self::INVALID_AUTHORIZATION => ['code' => '403', 'message' => 'api.response.error.request_unauthorized'],
        self::RESOURCE_NOT_FOUND => ['code' => '404', 'message' => 'api.response.error.resource_not_found'],
        self::METHOD_NOT_ALLOWED => ['code' => '405', 'message' => 'api.response.error.request_method_not_allowed'],
        self::REQ_TIME_OUT => ['code' => '408', 'message' => 'api.response.error.request_timed_out'],
        self::INTERNAL_ERR => ['code' => '500', 'message' => 'api.response.error.internal_error'],
        self::SERVICE_UNAVAIL => ['code' => '503', 'message' => 'api.response.error.service_unavailable'],
        self::GATEWAY_TIMEOUT => ['code' => '504', 'message' => 'api.response.error.gateway_timeout'],
        self::INVALID_AUTHENTICATION => ['code' => '1001', 'message' => 'api.response.error.invalid_auth_fields'],
        self::INVALID_REQ_DATA => ['code' => '1002', 'message' => 'api.response.error.invalid_request_data'],
        self::INVALID_ADDRESS_CODE => ['code' => '1003', 'message' => 'api.response.error.invalid_address_code'],
        self::INVALID_RESTAURANT_CODE => ['code' => '1004', 'message' => 'api.response.error.invalid_restaurant_code'],
        self::INVALID_CONTENT_TYPE => ['code' => '1005', 'message' => 'api.response.error.invalid_content_type'],
        self::INVALID_CONTENT_LENGTH => ['code' => '1006', 'message' => 'api.response.error.invalid_content_length'],
        self::PHONE_NUMBER_EXISTS => ['code' => '1007', 'message' => 'api.response.error.phone_number_exists'],
        self::INVALID_MENU_ITEM_CODE => ['code' => '1008', 'message' => 'api.response.error.invalid_menu_item_code'],
        self::INVALID_GEO_POINT => ['code' => '1009', 'message' => 'api.response.error.invalid_geo_point'],
        self::INVALID_PHONE_NUMBER => ['code' => '1010', 'message' => 'api.response.error.invalid_phone_number'],
        self::INVALID_EMAIL_FORMAT => ['code' => '1012', 'message' => 'api.response.error.invalid_email_data'],
        self::INVALID_USERNAME => ['code' => '1013', 'message' => 'api.response.error.invalid_username'],
        self::INVALID_CRED => ['code' => '1014', 'message' => 'api.response.error.invalid_credentials'],
        self::INVALID_AUTH_TOKEN => ['code' => '1015', 'message' => 'api.response.error.invalid_auth_token'],
        self::TOKEN_EXPIRED => ['code' => '1016', 'message' => 'api.response.error.auth_token_expired'],
        self::USERNAME_EXISTS => ['code' => '1017', 'message' => 'api.response.error.username_exists'],
        self::EMAIL_EXISTS => ['code' => '1018', 'message' => 'api.response.error.email_exists'],
        self::INVALID_REFRESH_TOKEN => ['code' => '1021', 'message' => 'api.response.error.invalid_refresh_token'],
        self::EXPIRED_REFRESH_TOKEN => ['code' => '1022', 'message' => 'api.response.error.expired_refresh_token'],
        self::INVALID_OLD_PASS => ['code' => '1018', 'message' => 'api.response.error.invalid_old_pass'],
        self::INVALID_NEW_PASS_FORMAT =>
            ['code' => '1019', 'message' => 'api.response.error.invalid_newpass_format']
        ,
        self::DISABLEDUSER => ['code' => '1020', 'message' => 'api.response.error.disabled_user'],
        self::INVALID_CONFIRM_PASS =>
            ['code' => '1021', 'message' => 'api.response.error.invalid_confirm_password']
        ,
    ];
}
