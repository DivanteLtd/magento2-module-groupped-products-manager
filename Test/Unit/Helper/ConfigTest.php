<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Divante\GroupedProductsManager\Helper\Config;

/**
 * Class ConfigTest.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Config
     */
    private $configModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->configModel         = $this->objectManagerHelper->getObject(
            Config::class,
            []
        );
    }

    /**
     * test isEnabledAttributesVisibility() method.
     */
    public function testIsEnabledAttributesVisibility()
    {
        $this->assertInternalType('bool', $this->configModel->isEnabledAttributesVisibility());
    }

    /**
     * test isEnabledAddToCart() method.
     */
    public function testIsEnabledAddToCart()
    {
        $this->assertInternalType('bool', $this->configModel->isEnabledAddToCart());
    }

    /**
     * test isEnabledAddToCartSimpleProduct() method.
     */
    public function testIsEnabledAddToCartSimpleProduct()
    {
        $this->assertInternalType('bool', $this->configModel->isEnabledAddToCartSimpleProduct());
    }

    /**
     * test isEnabledOutOfStockNotification() method.
     */
    public function testIsEnabledOutOfStockNotification()
    {
        $this->assertInternalType('bool', $this->configModel->isEnabledOutOfStockNotification());
    }
}
