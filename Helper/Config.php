<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config.
 */
class Config extends AbstractHelper
{
    /**
     * Configuration path for attributes visibility feature
     *
     * @var string
     */
    const CONFIG_ATTRIBUTES_VISIBILITY_XPATH = 'divante/grouped_products_manager/enable_attributes_visibility';

    /**
     * Configuration path for add to cart button feature
     *
     * @var string
     */
    const CONFIG_ADD_TO_CART_XPATH = 'divante/grouped_products_manager/enable_addtocart';

    /**
     * Configuration path for add to cart button should add simple product
     *
     * @var string
     */
    const CONFIG_ADD_TO_CART_SIMPLE_XPATH = 'divante/grouped_products_manager/addtocart_simple';

    /**
     * Configuration path for out of stock notification feature
     *
     * @var string
     */
    const CONFIG_OUT_OF_STOCK_NOTIFICATION_XPATH = 'divante/grouped_products_manager/enable_outofstock';

    /**
     * @return bool
     */
    public function isEnabledAttributesVisibility()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ATTRIBUTES_VISIBILITY_XPATH);
    }

    /**
     * @return bool
     */
    public function isEnabledAddToCart()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ADD_TO_CART_XPATH);
    }

    /**
     * @return bool
     */
    public function isEnabledAddToCartSimpleProduct()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ADD_TO_CART_SIMPLE_XPATH);
    }

    /**
     * @return bool
     */
    public function isEnabledOutOfStockNotification()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_OUT_OF_STOCK_NOTIFICATION_XPATH);
    }
}
