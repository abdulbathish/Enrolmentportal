# Election Enrollment System - eSignet OIDC Integration Reference

Welcome to the Election Enrollment System GitHub repository! This project serves as a **reference implementation** to demonstrate how to integrate with eSignet using OpenID Connect (OIDC) authentication. This system showcases user enrollment for elections using either voter ID credentials or eSignet OAuth authentication.

**Purpose**: This codebase is built as a learning resource and reference guide for developers who want to understand how to implement eSignet OIDC integration in their applications. It provides practical examples of JWT handling, OAuth flows, and secure authentication patterns.

## What You'll Learn

- How to configure eSignet OIDC authentication
- JWT token verification and handling
- OAuth 2.0 authorization code flow implementation
- Secure session management
- Integration patterns for identity verification systems

## Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/abdulbathish/Enrolmentportal.git
   cd Enrolmentportal
   ```

2. **Configure Constants**:

   The system uses a `constants.php` file to store all configuration values required for eSignet integration. Create this file in the root directory with the following structure:

   ```php
   <?php
   // eSignet OIDC Configuration
   $private_key = <<<EOPK
   -----BEGIN PRIVATE KEY-----
 
   -----END PRIVATE KEY-----
   EOPK;

   // eSignet OIDC Client Configuration
   define('CLIENT_PRIVATE_KEY', $private_key);
   define('CLIENT_ASSERTION_TYPE', 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer');
   define('ESIGNET_SERVICE_URL', "https://esignet.mecstage.mosip.net");
   define('USERINFO_PRIVATE_KEY', $private_key);
   define('CLIENT_ID', '4wIFOn6o0ZtVu6VWzkCa5RKAkr6DD2MMIvtdGPORhkU');
   
   // OIDC Parameters
   define('SCOPE','openid profile');
   define('STATE','scpe1234');
   define('ACR_VALUES','mosip:idp:acr:generated-code mosip:idp:acr:biometrics mosip:idp:acr:linked-wallet');
   define('CLAIMS','{"userinfo":{"individual_id":{"essential":true}, "name#en":{"essential":true},"name":{"essential":true},"phone_number":{"essential":true},"email":{"essential":true},"picture":{"essential":false},"gender":{"essential":true},"birthdate":{"essential":true},"address":{"essential":true}},"id_token":{}}');
   define('CLAIMS_LOCALES','en');
   define('UI_LOCALES','en-US');
   
   // Application Configuration
   define('CALLBACK_URL', 'http://10.13.13.42/Enrolmentportal/callback.php');
   
   // Database Configuration
   define("DB_HOST", "localhost");
   define("DB_USERNAME", "root");
   define("DB_PASSWORD", "");
   define("DB_NAME", "voter_database");
   ?>
   ```

   **Configuration Guide**:
   - `CLIENT_PRIVATE_KEY`: Your application's private key for JWT signing
   - `ESIGNET_SERVICE_URL`: eSignet service endpoint URL
   - `CLIENT_ID`: Your registered client ID in eSignet
   - `SCOPE`: OpenID Connect scopes (openid is required)
   - `CLAIMS`: Specific user information claims you want to request
   - `CALLBACK_URL`: Your application's callback URL after authentication
   - `ACR_VALUES`: Authentication Context Class Reference values supported by eSignet

## Key Features & Reference Implementation

This system demonstrates:

1. **eSignet OIDC Integration**: Complete OAuth 2.0 authorization code flow implementation
2. **JWT Token Handling**: Secure JWT verification and user info extraction
3. **Dual Authentication**: Support for both local voter ID and eSignet authentication
4. **Age Verification**: Automatic eligibility checking based on voter age
5. **Session Management**: Secure user session handling
6. **Verified Status**: Distinguished handling for eSignet-verified users

## Usage & Testing

- **Purpose**: This is a reference implementation for learning eSignet OIDC integration patterns
- **Age Verification**: The system checks voter eligibility based on legal voting age
- **eSignet Verification**: Users authenticated via eSignet receive a "verified" status indicator
- **Testing**: Visit `/clear.php` to reset voter tables for testing different scenarios

## Important Notes

- This is a **reference implementation** designed for learning and understanding eSignet OIDC integration
- In production environments, private keys should be securely managed and never stored as plain text
- Consider using environment variables or secure key management systems for sensitive configuration
- The database and authentication patterns shown here are for demonstration purposes

## File Structure

- `constants.php`: All configuration values and eSignet OIDC settings
- `callback.php`: OAuth callback handler for eSignet authentication
- `helpers/`: JWT verification and user session management utilities
- `connection.php`: Database connection management
- `login.php`: Authentication entry point
- `dashboard.php`: Main application interface

This reference implementation provides a solid foundation for understanding how to integrate eSignet OIDC authentication into your applications.
