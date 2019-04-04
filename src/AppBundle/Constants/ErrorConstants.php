<?php
/**
 *  Error Constants file for Storing Error Message codes and Message Text for Application.
 *
 *  @category Constants
 */

namespace AppBundle\Constants;

final class ErrorConstants
{
    const INCOMPLETE_REQ = 'INCOMPLETEREQ';
    const INTERNAL_ERR = 'INTERNALERR';
    const INVALID_CONTENT_TYPE = 'INVALIDCONTENTTYPE';
    const INVALID_CONTENT_LENGTH = 'INVALIDCONTENTLEN';
    const INVALID_REQ_DATA = 'INVALIDREQDATA';
    const INVALID_AUTH_CONTENT = 'INVALIDAUTHCONTENT';
    const RESOURCE_NOT_FOUND = 'NORESOURCEFOUND';
    const INVALID_AUTHENTICATION = 'INVALIDAUTHENTICATION';
    const INVALID_AUTHORIZATION = 'INVALIDAUTHORIZATION';
    const METHOD_NOT_ALLOWED = 'METHODNOTALLOWED';
    const REQ_TIME_OUT = 'REQTIMEOUT';
    const SERVICE_UNAVAIL = 'SERVICEUNAVAIL';
    const INVALID_SATN = 'INVALIDSATN';
    const INVALID_MERCHANT = 'INVALIDMERCHANT';
    const INVALID_TERMINAL = 'INVALIDTERMINAL';
    const INVALID_CURRENCY_CODE = 'INVALIDCURRENCYCODE';
    const INVALID_AMOUNT = 'INVALIDAMOUNT';
    const INVALID_CONTENTMD5 = 'INVALIDCONTENTMD5';
    const INVALID_DATE_TIME = 'INVALIDDATETIME';
    const EMPTY_AUTH_HEADER = 'EMPTYAUTHHEAD';
    const MISSING_AUTH_RDN = 'MISSINGRDN';
    const MISSING_AUTH_FIELD = 'MISSINGAUTHFIELD';
    const GATEWAY_TIMEOUT = 'GATEWAYTIMEOUT';
    const BAD_GATEWAY = 'BADGATEWAY';
    const AUTHENTICATION_EXPIRY = 'AUTHENTICATIONEXPIRY';
    const INVALID_SUPPLIER_CODE = 'INVALIDSUPPLIERCODE';
    const INVALID_PRODUCT_ID = 'INVALIDPRODID';
    const INVALID_PHONE_NUMBER = 'INVALIDPHONENO';
    const SERVICE_ACCESS_NOT_ALLOWED = 'SERVICENOTALLOWED';
    const INSUFFICIENT_MERCHANT_BALANCE = 'INSUFFICIENTMERCHANTBALANCE';
    const INVALID_MERCHANT_NAME = 'INVALIDMERCHANTNAME';
    const INVALID_BUSINESS_NAME = 'INVALIDBUSINESSNAME';
    const INVALID_MERCHANT_STATUS = 'INVALIDMERCHANTSTATUS';
    const INVALID_EMAIL = 'INVALIDEMAIL';
    const INVALID_MERCHANT_API_KEY = 'INVALIDMERCHANTAPIKEY';
    const INVALID_TERMINAL_STATUS = 'INVALIDTERMINALSTATUS';
    const INVALID_TERMINAL_IDENTIFIER_ID = 'INVALIDTERMINALIDENTIFIERID';
    const MERCHANT_ID_PREEXIST = 'MERCHANTIDPREEXIST';
    const TERMINAL_ID_PREEXIST = 'TERMINALIDPREEXIST';
    const INVALID_USERNAME = 'INVALIDUSERNAME';
    const INVALID_PASS = 'INVALIDPASS';
    const INVALID_CRED = 'INVALIDCRED';
    const INVALID_AUTH_TOKEN = 'INVALIDAUTHTOKEN';
    const TOKEN_EXPIRED = 'TOKENEXPIRED';
    const INVALID_ENABLE_VAL = 'INVALIDENABLEVAL';
    const INVALID_USER_ROLE = 'INVALIDUSERROLE';
    const USERNAME_EXISTS = 'USERNAMEPREEXIST';
    const PHONE_NUMBER_EXISTS = 'PHONENUMBEREXIST';
    const EMAIL_EXISTS = 'EMAILPREEXISTS';
    const INVALID_REFRESH_TOKEN = 'INVALIDREFRESHTOKEN';
    const EXPIRED_REFRESH_TOKEN = 'EXPIREDREFRESHTOKEN';
    const INVALID_MONTH = 'INVALIDMONTH';
    const INVALID_YEAR = 'INVALIDYEAR';
    const INVALID_STATUS = 'INVALIDSTATUS';
    const INVALID_OLDPASS = 'INVALIDOLDPASS';
    const INVALID_NEWPASSFORMAT = 'INVALIDNEWPASSFORMAT';
    const DISABLEDUSER = 'DISABLEDUSER';

    public static $errorCodeMap = [
        self::AUTHENTICATION_EXPIRY => ['code' => '401', 'message' => 'api.response.error.invalid_authentication'],
        self::INVALID_AUTHORIZATION => ['code' => '403', 'message' => 'api.response.error.request_unauthorized'],
        self::RESOURCE_NOT_FOUND => ['code' => '404', 'message' => 'api.response.error.resource_not_found'],
        self::METHOD_NOT_ALLOWED => ['code' => '405', 'message' => 'api.response.error.request_method_not_allowed'],
        self::REQ_TIME_OUT => ['code' => '408', 'message' => 'api.response.error.request_timed_out'],
        self::INTERNAL_ERR => ['code' => '500', 'message' => 'api.response.error.internal_error'],
        self::BAD_GATEWAY => ['code' => '502', 'message' => 'api.response.error.bad_gateway'],
        self::SERVICE_UNAVAIL => ['code' => '503', 'message' => 'api.response.error.service_unavailable'],
        self::GATEWAY_TIMEOUT => ['code' => '504', 'message' => 'api.response.error.gateway_timeout'],
        self::INVALID_AUTHENTICATION => ['code' => '1001', 'message' => 'api.response.error.invalid_auth_fields'],
        self::INCOMPLETE_REQ => ['code' => '1002', 'message' => 'api.response.error.incomplete_req'],
        self::INVALID_REQ_DATA => ['code' => '1003', 'message' => 'api.response.error.invalid_request_data'],
        self::INVALID_SATN => ['code' => '1004', 'message' => 'api.response.error.invalid_satn'],
        self::INVALID_AMOUNT => ['code' => '1005', 'message' => 'api.response.error.invalid_amount'],
        self::INVALID_CURRENCY_CODE => ['code' => '1006', 'message' => 'api.response.error.invalid_currency_code'],
        self::INVALID_DATE_TIME => ['code' => '1007', 'message' => 'api.response.error.invalid_date_time'],
        self::INVALID_CONTENT_TYPE => ['code' => '1008', 'message' => 'api.response.error.invalid_content_type'],
        self::INVALID_CONTENT_LENGTH => ['code' => '1009', 'message' => 'api.response.error.invalid_content_length'],
        self::INVALID_CONTENTMD5 => ['code' => '1010', 'message' => 'api.response.error.invalid_content_md5'],
        self::EMPTY_AUTH_HEADER => ['code' => '1011', 'message' => 'api.response.error.empty_auth_header'],
        self::MISSING_AUTH_RDN => ['code' => '1012', 'message' => 'api.response.error.empty_auth_rdn'],
        self::MISSING_AUTH_FIELD => ['code' => '1013', 'message' => 'api.response.error.empty_auth_fields'],
        self::PHONE_NUMBER_EXISTS => ['code' => '1014', 'message' => 'api.response.error.phone_number_exists'],
        self::INVALID_TERMINAL => ['code' => '1015', 'message' => 'api.response.error.invalid_terminal_id'],
        self::INVALID_SUPPLIER_CODE => ['code' => '1016', 'message' => 'api.response.error.invalid_supplier_code'],
        self::INVALID_PRODUCT_ID => ['code' => '1017', 'message' => 'api.response.error.invalid_product_id'],
        self::INVALID_PHONE_NUMBER => ['code' => '1018', 'message' => 'api.response.error.invalid_phone_number'],
        self::SERVICE_ACCESS_NOT_ALLOWED => ['code' => '1019', 'message' => 'api.response.error.service_not_allowed'],
        self::INSUFFICIENT_MERCHANT_BALANCE =>
            ['code' => '1020', 'message' => 'api.response.error.insufficient_merchant_balance'],
        self::INVALID_MERCHANT_NAME => ['code' => '1021', 'message' => 'api.response.error.invalid_merchant_name'],
        self::INVALID_BUSINESS_NAME => ['code' => '1022', 'message' => 'api.response.error.invalid_business_name'],
        self::INVALID_MERCHANT_STATUS => ['code' => '1023', 'message' => 'api.response.error.invalid_merchant_status'],
        self::INVALID_EMAIL => ['code' => '1024', 'message' => 'api.response.error.invalid_email'],
        self::INVALID_MERCHANT_API_KEY =>
            ['code' => '1025', 'message' => 'api.response.error.invalid_merchant_api_key'],
        self::INVALID_TERMINAL_STATUS => ['code' => '1026', 'message' => 'api.response.error.invalid_terminal_status'],
        self::INVALID_TERMINAL_IDENTIFIER_ID =>
            ['code' => '1027', 'message' => 'api.response.error.invalid_terminal_identifier_id'],
        self::MERCHANT_ID_PREEXIST =>
            ['code' => '1028', 'message' => 'api.response.error.merchant_id_preexist'],
        self::TERMINAL_ID_PREEXIST =>
            ['code' => '1029', 'message' => 'api.response.error.terminal_id_preexist'],
        self::INVALID_PASS => ['code' => '1030', 'message' => 'api.response.error.invalid_password_format'],
        self::INVALID_MONTH => ['code' => '1031', 'message' => 'api.response.error.invalid_month'],
        self::INVALID_YEAR => ['code' => '1032', 'message' => 'api.response.error.invalid_year'],
        self::INVALID_STATUS => ['code' => '1033', 'message' => 'api.response.error.invalid_status'],
        self::INVALID_USERNAME => ['code' => '1034', 'message' => 'api.response.error.invalid_username'],
        self::INVALID_CRED => ['code' => '1035', 'message' => 'api.response.error.invalid_credentials'],
        self::INVALID_AUTH_TOKEN => ['code' => '1036', 'message' => 'api.response.error.invalid_auth_token'],
        self::TOKEN_EXPIRED => ['code' => '1037', 'message' => 'api.response.error.auth_token_expired'],
        self::INVALID_ENABLE_VAL => ['code' => '1038', 'message' => 'api.response.error.invalid_enabled_val'],
        self::INVALID_USER_ROLE => ['code' => '1039', 'message' => 'api.response.error.invalid_user_role'],
        self::USERNAME_EXISTS => ['code' => '1040', 'message' => 'api.response.error.username_exists'],
        self::EMAIL_EXISTS => ['code' => '1041', 'message' => 'api.response.error.email_exists'],
        self::INVALID_REFRESH_TOKEN => ['code' => '1042', 'message' => 'api.response.error.invalid_refresh_token'],
        self::EXPIRED_REFRESH_TOKEN => ['code' => '1043', 'message' => 'api.response.error.expired_refresh_token'],
        self::INVALID_OLDPASS => ['code' => '1044', 'message' => 'api.response.error.invalid_old_pass'],
        self::INVALID_NEWPASSFORMAT => ['code' => '1045', 'message' => 'api.response.error.invalid_newpass_format'],
        self::DISABLEDUSER => ['code' => '1046', 'message' => 'api.response.error.disabled_user'],
    ];
}