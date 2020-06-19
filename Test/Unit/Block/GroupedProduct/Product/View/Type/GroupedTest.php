<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Block\GroupedProduct\Product\View\Type;

use Divante\GroupedProductsManager\Block\GroupedProduct\Product\View\Type\Grouped;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Divante\GroupedProductsManager\Helper\OutOfStock;
use Magento\Catalog\Model\Product\Attribute\Repository as ProductAttributeRepository;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Catalog\Helper\Output;
use Magento\Directory\Model\PriceCurrency;
use Magento\Catalog\Model\ProductFactory;
use Divante\GroupedProductsManager\Helper\Config;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class GroupedTest.
 */
class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var SearchCriteriaBuilderFactory|MockObject
     */
    private $searchCriteriaBuilderFactoryMock;

    /**
     * @var ProductAttributeRepository|MockObject
     */
    private $productAttributeRepositoryMock;

    /**
     * @var Output|MockObject
     */
    private $outputHelperMock;

    /**
     * @var PriceCurrency|MockObject
     */
    private $priceCurrencyMock;

    /**
     * @var ProductFactory|MockObject
     */
    private $productFactoryMock;

    /**
     * @var Config|MockObject
     */
    private $moduleConfigMock;

    /**
     * @var OutOfStock|MockObject
     */
    private $outOfStockHelperMock;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var ArrayUtils|MockObject
     */
    private $arrayUtilsMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SearchCriteria|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var Grouped
     */
    private $groupedModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->searchCriteriaBuilderFactoryMock = $this->getMockBuilder(
            SearchCriteriaBuilderFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(
            SearchCriteriaBuilder::class
        )->disableOriginalConstructor()->getMock();

        $this->searchCriteriaMock = $this->getMockBuilder(
            SearchCriteria::class
        )->disableOriginalConstructor()->getMock();

        $this->searchCriteriaBuilderFactoryMock->method('create')
            ->willReturn($this->searchCriteriaBuilderMock);

        $this->searchCriteriaBuilderMock->method('create')->willReturn($this->searchCriteriaMock);

        $this->productAttributeRepositoryMock = $this->getMockBuilder(
            ProductAttributeRepository::class
        )->disableOriginalConstructor()->setMethods(
            [
                'getList',
                'getItems',
            ]
        )->getMock();

        $this->outputHelperMock = $this->getMockBuilder(
            Output::class
        )->disableOriginalConstructor()->getMock();

        $this->priceCurrencyMock = $this->getMockBuilder(
            PriceCurrency::class
        )->disableOriginalConstructor()->setMethods(['convertAndFormat'])->getMock();

        $this->productFactoryMock = $this->getMockBuilder(
            ProductFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->productMock = $this->getMockBuilder(
            Product::class
        )->disableOriginalConstructor()->getMock();

        $this->productFactoryMock->method('create')->willReturn($this->productMock);

        $this->moduleConfigMock = $this->getMockBuilder(
            Config::class
        )->disableOriginalConstructor()->getMock();

        $this->outOfStockHelperMock = $this->getMockBuilder(
            OutOfStock::class
        )->disableOriginalConstructor()->setMethods(['getProductAlertUrl'])->getMock();

        $this->contextMock = $this->getMockBuilder(
            Context::class
        )->disableOriginalConstructor()->getMock();

        $this->arrayUtilsMock = $this->getMockBuilder(
            ArrayUtils::class
        )->disableOriginalConstructor()->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->groupedModel = $this->objectManagerHelper->getObject(
            Grouped::class,
            [
                'searchCriteriaBuilderFactory' => $this->searchCriteriaBuilderFactoryMock,
                'productAttributeRepository' => $this->productAttributeRepositoryMock,
                'outputHelper' => $this->outputHelperMock,
                'priceCurrency' => $this->priceCurrencyMock,
                'productFactory' => $this->productFactoryMock,
                'outOfStoryHelper' => $this->outOfStockHelperMock,
                'moduleConfig' => $this->moduleConfigMock,
                'context' => $this->contextMock,
                'arrayUtils' => $this->arrayUtilsMock,
            ]
        );
    }

    /**
     * test getProductStockAlertUrl() method.
     */
    public function testGetProductStockAlertUrl()
    {
        $this->outOfStockHelperMock->method('getProductAlertUrl')->with($this->productMock);

        $this->groupedModel->getProductStockAlertUrl($this->productMock);
    }

    /**
     * test getOutputHelper() method.
     */
    public function testGetOutputHelper()
    {
        $this->assertSame($this->outputHelperMock, $this->groupedModel->getOutputHelper());
    }

    /**
     * test getModuleConfigHelper() method.
     */
    public function testGetModuleConfigHelper()
    {
        $this->assertSame($this->moduleConfigMock, $this->groupedModel->getModuleConfigHelper());
    }

    /**
     * test resolveVisibleAttributes() method.
     *
     * @param $productId
     *
     * @dataProvider productIdProvider
     */
    public function testResolveVisibleAttributes($productId)
    {
        $visibleAttributes = 'a:2:{i:1;s:23:"145,146,147,148,149,150";i:2;s:3:"149";}';
        $attributesValues =
            [
                null,
                'Żółty',
                123.5,
                '',
            ];

        $productMock = $this->getMockBuilder(
            Product::class
        )->disableOriginalConstructor()->setMethods(['getProductsAttributesVisibility'])->getMock();
        $productMock->expects($this->once())->method('getProductsAttributesVisibility')->willReturn($visibleAttributes);

        $itemMock = $this->getMockBuilder(
            Product::class
        )->disableOriginalConstructor()->getMock();

        $itemMock->id = $productId;

        $productResourceMock = $this->getMockBuilder(
            ProductResource::class
        )->disableOriginalConstructor()->getMock();

        $itemMock->expects($this->once())->method('getResource')->willReturn($productResourceMock);
        $itemMock->expects($this->atLeastOnce())->method('getId')->willReturn($productId);
        $productResourceMock->expects($this->once())->method('load')->with($this->productMock, $productId);

        if (null !== $productId) {
            $this->productAttributeRepositoryMock->expects($this->once())->method('getList')->willReturnSelf();
            $this->productAttributeRepositoryMock->expects($this->once())
                ->method('getItems')
                ->willReturn(
                    $this->prepareAttributesCollection(
                        $attributesValues
                    )
                );

            $this->productMock->expects($this->exactly(count($attributesValues)))->method('hasData');
        }

        $this->groupedModel->resolveVisibleAttributes($productMock, $itemMock);
    }

    /**
     * @return array
     */
    public static function productIdProvider()
    {
        return [
            [1],
            [null],
        ];
    }

    /**
     * @param $attributeValues
     *
     * @return array
     */
    public function prepareAttributesCollection($attributeValues)
    {
        $result = [];

        foreach ($attributeValues as $value) {
            $attributeMock = $this->getMockBuilder(
                Attribute::class
            )->disableOriginalConstructor()->setMethods(
                [
                    'getFrontend',
                    'getValue',
                    'getStoreLabel',
                ]
            )->getMock();

            $attributeMock->value = $value;

            $attributeMock->expects($this->atLeastOnce())->method('getFrontend')->willReturnSelf();
            $attributeMock->expects($this->atLeastOnce())->method('getValue')->willReturn($attributeMock->value);
            $attributeMock->expects($this->atLeastOnce())->method('getStoreLabel')->willReturn('Label');

            $result[] = $attributeMock;
        }

        return $result;
    }
}
