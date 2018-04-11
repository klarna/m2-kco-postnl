<h2>Klarna_KcoPostnl module</h2>

## Overview

The Klarna_KcoPostnl module adds support for the tig/postnl-magento2 PostNL module to Klarna Checkout in Magento 2.x.

## Implementation Details

The Klarna_KcoPostnl module:

 * adds plugin on `Klarna\Core\Model\Api\Builder::setRequest` to add an attachment for the Klarna Checkout API to pass EMD data (See https://developers.klarna.com/api/#payments-api__attachment__body__other_delivery_address)

## Dependencies

You can find the list of modules that have dependencies on Klarna_KcoPostnl module, in the `require` section of the `composer.json` file located in the same directory as this `README.md` file.

**NOTE**: Due to frequent changes in the tig/postnl-magento2 module that break Klarna Checkout support, we lock to a specific version of that module. We will review and update to newer versions of tig/postnl-magento2 on a regular basis but there may be delays from their release until our support for it.

## Extension Points

The Klarna_KcoPostnl module does not provide any specific extension points. You can extend it using the Magento extension mechanism.

For more information about Magento extension mechanism, see [Magento plug-ins](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html) and [Magento dependency injection](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/depend-inj.html).

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
