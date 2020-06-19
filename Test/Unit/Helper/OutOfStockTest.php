<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Url;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Divante\GroupedProductsManager\Helper\OutOfStock;
use Magento\Framework\App\Helper\Context;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class OutOfStockTest.
 */
class OutOfStockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var UrlHelper|MockObject
     */
    private $urlHelperMock;

    /**
     * @var Url|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var OutOfStock
     */
    private $outOfStockModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();

        $this->urlHelperMock = $this->getMockBuilder(
            UrlHelper::class
        )->disableOriginalConstructor()->setMethods(['getEncodedUrl'])->getMockForAbstractClass();

        $this->urlBuilderMock = $this->getMockBuilder(Url::class)->disableOriginalConstructor()->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->outOfStockModel = $this->objectManagerHelper->getObject(
            OutOfStock::class,
            [
                'context' => $this->contextMock,
                'urlHelper' => $this->urlHelperMock,
                '_urlBuilder' => $this->urlBuilderMock,
            ]
        );
    }

    /**
     * test getProductAlertUrl() method.
     */
    public function testGetProductAlertUrl()
    {
        $productId = 123;

        $productMock = $this->getMockBuilder(
            Product::class
        )->disableOriginalConstructor()->setMethods(['getId'])->getMock();

        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->urlHelperMock->expects($this->once())->method('getEncodedUrl')->willReturn('');

        $this->outOfStockModel->getProductAlertUrl($productMock);
    }
}
