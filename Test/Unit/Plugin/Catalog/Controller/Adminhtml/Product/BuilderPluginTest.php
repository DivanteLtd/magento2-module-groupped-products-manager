<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Plugin\Catalog\Controller\Adminhtml\Product;

use Divante\GroupedProductsManager\Plugin\Catalog\Controller\Adminhtml\Product\BuilderPlugin;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;
use Divante\GroupedProductsManager\Helper\Config;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as MagentoBuilder;
use Magento\Framework\App\Request\Http;

/**
 * Class BuilderPluginTest.
 */
class BuilderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Config|MockObject
     */
    private $moduleConfigMock;

    /**
     * @var BuilderPlugin
     */
    private $builderPluginModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->moduleConfigMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->builderPluginModel = $this->objectManagerHelper->getObject(
            BuilderPlugin::class,
            [
                'moduleConfig' => $this->moduleConfigMock,
            ]
        );
    }

    /**
     * test aroundBuild() method.
     */
    public function testAroundBuild()
    {
        $proceed = $this->prepareProceed();
        $subjectMock = $this->getMockBuilder(MagentoBuilder::class)->disableOriginalConstructor()->getMock();
        $requestMock = $this->getMockBuilder(
            Http::class
        )->disableOriginalConstructor()->setMethods(['getParam'])->getMock();

        $this->moduleConfigMock->expects($this->once())->method('isEnabledAttributesVisibility')->willReturn(true);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('links')
            ->willReturn(
                [
                    'associated' => [
                        0 => [
                            'visible_attributes' => [
                                '1',
                                '2',
                                '3',
                            ],
                            'id' => '1',
                        ],
                    ],
                ]
            );

        $this->assertInstanceOf(
            Product::class,
            $this->builderPluginModel->aroundBuild($subjectMock, $proceed, $requestMock)
        );
    }

    /**
     * @return \Closure
     */
    public function prepareProceed()
    {
        $productMock = $this->getMockBuilder(
            Product::class
        )->disableOriginalConstructor()->setMethods(['getTypeId'])->getMock();

        $productMock->expects($this->once())->method('getTypeId')->willReturn('grouped');

        return function () use ($productMock) {
            return $productMock;
        };
    }

}