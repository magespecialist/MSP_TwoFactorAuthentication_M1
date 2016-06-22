# MSP_TwoFactorAuthentication_M1

MSP_TwoFactorAuth is a Magento 1 module to activate a "two factor authentication" procedure on your Magento backend.

This module is fully compliant with Google Authenticator application for smartphones and easy to set-up.

This module is on **BETA**, we recommend to **test it before using on production**.

## Setup

- Copy this module on your Magento root
- Flush your cache
- Go to System > Config > MageSpecialist > Two Factor Authentication
- Turn on by setting "enabled" to "Yes"
- Go to "System > My Account" and set "Enable Two Factor Authentication" to "Yes"
- Follow the on screen instructions

### Did you mess up with authentication factor?

If you messed up with two factor authentication try to not panic ;) and follow one modes listed below:

#### Panic mode:

- Edit app/etc/modules/MSP_TwoFactorAuth.xml and set "active" to "false"
- Flush your cache

#### Standard mode:

- Open "admin_user" table with phpMyAdmin
- Locate your user
- Set "msp_tfa_enabled", "msp_tfa_activated" to 0
- Login your backend
