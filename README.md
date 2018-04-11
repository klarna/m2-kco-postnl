# Klarna_KcoPostnl module

## Overview

The Klarna_KcoPostnl Add-On module adds support for the [tig/postnl-magento2](https://github.com/tig-nl/postnl-magento2/) PostNL module to Klarna Checkout in Magento 2.x.

## Requirements
To use this module you must:
 * Have an account with [Klarna](https://www.klarna.com)
 * "Purchase" and install the Klarna Checkout for M2 module from the [Magento Marketplace](https://marketplace.magento.com/klarna-m2-checkout.html).
 * Have an account with PostNL

**NOTE:** Installing this module will also install the PostNL module for M2 provided by [TIG](https://tig.nl/) _locked at a specific, supported version_.

## Implementation Details

The Klarna_KcoPostnl module:

 * Adds plugin on `Klarna\Core\Model\Api\Builder::setRequest` to add an attachment for the Klarna Checkout API to pass EMD data (See https://developers.klarna.com/api/#payments-api__attachment__body__other_delivery_address)

## Dependencies

You can find the list of dependencies for the Klarna_KcoPostnl module in the `require` section of the `composer.json` file located in the same directory as this `README.md` file.

**NOTE**: Due to frequent changes in the tig/postnl-magento2 module that break Klarna Checkout support, we lock to a specific version of that module. We will review and update to newer versions of tig/postnl-magento2 on a regular basis but there may be delays from their release until our support for it.

## Extension Points

The Klarna_KcoPostnl module does not provide any specific extension points. You can extend it using the Magento extension mechanism.

For more information about Magento extension mechanism, see [Magento plug-ins](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html) and [Magento dependency injection](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/depend-inj.html).

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).

## License

Copyright 2018 Klarna Bank AB (publ)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
