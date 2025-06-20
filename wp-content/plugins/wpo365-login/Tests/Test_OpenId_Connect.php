<?php

namespace Wpo\Tests;

use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Id_Token_Service;
use \Wpo\Services\Options_Service;
use \Wpo\Services\Request_Service;
use \Wpo\Services\User_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Tests\Test_OpenId_Connect')) {

    class Test_OpenId_Connect
    {

        private $id_token = null;

        public function test_application_id()
        {
            $test_result = new Test_Result('Application (Client) ID has been configured', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_BLOCKING);
            $test_result->passed = true;

            $application_id = Options_Service::get_aad_option('application_id');

            if (empty($application_id)) {
                $test_result->passed = false;
                $test_result->message = "Application ID is not configured but needed for OpenID Connect based Single Sign-On and many of the supported features. It may be omitted in case of SAML 2.0 based Single Sign-On. Please copy the 'Application (Client) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            } elseif (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/', $application_id)) {
                $test_result->passed = false;
                $test_result->message = "Application ID is not a valid GUID but needed for OpenID Connect based Single Sign-On and many of the supported features. It may be omitted in case of SAML 2.0 based Single Sign-On. Please copy the 'Application (Client) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_oidc_flow()
        {
            // Do not perform this test if support for multi-tenancy has been enabled
            if (Options_Service::get_global_boolean_var('multi_tenanted')) {
                return;
            }

            $oidc_flow = Options_Service::get_aad_option('oidc_flow');
            $test_result = new Test_Result('The OpenID Connect <strong>Authorization Code User Flow</strong> has been configured', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_CRITICAL);
            $test_result->passed = true;

            if ($oidc_flow != 'code') {
                $test_result->passed = false;
                $test_result->message = "Starting with v18.0 it is recommended to configure the OpenID Connect <strong>Authorization Code User Flow</strong> in favor of the <strong>Hybrid User Flow</strong>. Please click the <em>Read more</em> link and consult the online documentation.";
                $test_result->more_info = 'https://docs.wpo365.com/article/156-why-the-authorization-code-user-flow-is-now-recommended';
                $test_result->fix = array(
                    array(
                        'op' => 'replace',
                        'value' => array(
                            'oidcFlow' => 'code',
                        ),
                    ),
                );
                return $test_result;
            }

            return $test_result;
        }

        public function test_hybrid_flow()
        {
            // Do not perform this test if support for multi-tenancy has nnot been enabled
            if (!Options_Service::get_global_boolean_var('multi_tenanted')) {
                return;
            }

            $oidc_flow = Options_Service::get_aad_option('oidc_flow');
            $test_result = new Test_Result('The OpenID Connect <strong>Hybrid User Flow</strong> has been configured', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_CRITICAL);
            $test_result->passed = true;

            if ($oidc_flow == 'code') {
                $test_result->passed = false;
                $test_result->message = "It appears that you have configured support for <strong>multi-tenancy</strong> and hence you must configure the <strong>OpenID Connect Hybrid User Flow</strong> to enable users from other tenants to sign into your website. Currently you have selected the <strong>Authorization Code User Flow</strong>. Please click the <em>Read more</em> link and consult the online documentation.";
                $test_result->more_info = 'https://docs.wpo365.com/article/47-support-for-multitenant-apps';
                $test_result->fix = array(
                    array(
                        'op' => 'replace',
                        'value' => array(
                            'oidcFlow' => 'hybrid',
                        ),
                    ),
                );
                return $test_result;
            }

            return $test_result;
        }

        public function test_application_secret()
        {
            // Only test for the application secret if the authorization code user flow has been configured
            $oidc_flow = Options_Service::get_aad_option('oidc_flow');

            if ($oidc_flow != 'code') {
                return;
            }

            $test_result = new Test_Result('Application (Client) Secret has been configured', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_BLOCKING);
            $test_result->passed = true;

            $application_secret = Options_Service::get_aad_option('application_secret');

            if (empty($application_secret)) {
                $test_result->passed = false;
                $test_result->message = "An <em>Application (Client) Secret</em> is needed for the selected <em>OpenID Connect Flow (Auth.-Code)</em> but the required Application (Client) Secret has not been configured (on the <a href=\"#singleSignOn\">Single Sign-on</a> tab). Please consult the online documentation using the link below.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
                return $test_result;
            }

            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $application_secret)) {
                $test_result->passed = false;
                $test_result->message = 'Application (Client) Secret appears to be invalid. Possibly the secret\'s ID instead of its value has been copied from the corresonding page in Azure Portal.';
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
                return $test_result;
            }

            return $test_result;
        }

        public function test_redirect_url()
        {

            $test_result = new Test_Result('Redirect URL has been configured', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_BLOCKING);
            $test_result->passed = true;

            /**
             * @since 24.0 Filters the AAD Redirect URI e.g. to set it dynamically to the current host.
             */

            $redirect_url = Options_Service::get_aad_option('redirect_url');
            $redirect_url = apply_filters('wpo365/aad/redirect_uri', $redirect_url);

            if (empty($redirect_url)) {
                $test_result->passed = false;
                $test_result->message = "The Redirect URL is not configured but needed for OpenID Connect based Single Sign-On. It may be omitted in case of SAML 2.0 based Single Sign-On. Please copy the 'Redirect URI' from your Azure AD App registration's 'Authentication' page and paste it into the corresponding field on the plugin's <a href=\"#singleSignOn\">Single Sign-on</a> page.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_decode_id_token()
        {
            delete_site_option('wpo365_msft_key');
            delete_site_option('wpo365_msft_keys');

            $test_result = new Test_Result('Can decode the ID token', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_BLOCKING);
            $test_result->passed = true;

            Id_Token_Service::process_openidconnect_token(false);

            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);

            $this->id_token = $request->get_item('id_token');

            if (empty($this->id_token)) {
                $error_message = isset($_REQUEST['error_description']) ? \sanitize_text_field($_REQUEST['error_description']) : 'Could not process the ID token. Please check the <a href="#debug">debug log</a> for errors.';

                if (false !== WordPress_Helpers::stripos($error_message, 'AADSTS700054')) {
                    $application_id = Options_Service::get_aad_option('application_id');
                    $error_message = 'It appears you have configured the (OpenID Connect) <strong>Hybrid flow</strong> on the <a href="#singleSignOn">Single Sign-on</a> page but did not allow for <em>Implicit grant and hybrid flows</em> by checking the corresponding options in Azure AD for the App registration with ID ' . $application_id . ' on the <em>Authentication</em> page.';
                }

                $test_result->passed = false;
                $test_result->message = $error_message;
                $test_result->more_info = '';

                return $test_result;
            }

            $test_result->data = $this->id_token;
            return $test_result;
        }

        public function test_id_token_contains_email()
        {
            $test_result = new Test_Result('ID token contains email address', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_CRITICAL);
            $test_result->passed = true;

            if (empty($this->id_token)) {
                $test_result->passed = false;
                $test_result->message = 'ID token missing -> test skipped';
                $test_result->more_info = '';
            } elseif (empty($this->id_token->email)) {
                $test_result->passed = false;
                $test_result->message = "ID token does not contain email address. Please ensure that the user has a valid email address. If this is the case then please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'email' claim.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_id_token_contains_upn()
        {
            $test_result = new Test_Result('ID token contains user principal name', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_CRITICAL);
            $test_result->passed = true;

            if (empty($this->id_token)) {
                $test_result->passed = false;
                $test_result->message = 'ID token missing -> test skipped';
                $test_result->more_info = '';
            } elseif (empty($this->id_token->upn)) {
                $test_result->passed = false;
                $test_result->message = "ID token does not contain user principal name (upn). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'upn' claim.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_id_token_contains_given_name()
        {
            $test_result = new Test_Result('ID token contains first name', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_LOW);
            $test_result->passed = true;

            if (empty($this->id_token)) {
                $test_result->passed = false;
                $test_result->message = 'ID token missing -> test skipped';
                $test_result->more_info = '';
            } elseif (empty($this->id_token->given_name)) {
                $test_result->passed = false;
                $test_result->message = "ID token does not contain first name (given_name). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'given_name' claim. Please note that the latest version of the plugin will try to retrieve a user's profile from Microsoft Graph to update the corresponding WordPress user instead.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_id_token_contains_family_name()
        {
            $test_result = new Test_Result('ID token contains last name', Test_Result::CAPABILITY_OIC_SSO, Test_Result::SEVERITY_LOW);
            $test_result->passed = true;

            if (empty($this->id_token)) {
                $test_result->passed = false;
                $test_result->message = 'ID token missing -> test skipped';
                $test_result->more_info = '';
            } elseif (empty($this->id_token->family_name)) {
                $test_result->passed = false;
                $test_result->message = "ID token does not contain last name (family_name). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'family_name' claim. Please note that the latest version of the plugin will try to retrieve a user's profile from Microsoft Graph to update the corresponding WordPress user instead.";
                $test_result->more_info = 'https://docs.wpo365.com/article/154-aad-single-sign-for-wordpress-using-auth-code-flow';
            }

            return $test_result;
        }

        public function test_end()
        {

            if (empty($this->id_token)) {
                return;
            }

            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $request->set_item('wpo_usr', User_Service::user_from_id_token($this->id_token));
        }
    }
}
