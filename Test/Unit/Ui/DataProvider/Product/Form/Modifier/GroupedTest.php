<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Divante\GroupedProductsManager\Ui\DataProvider\Product\Form\Modifier\Grouped;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Catalog\Model\ProductLink\Repository;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Locator\RegistryLocator;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Url;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Divante\GroupedProductsManager\Helper\Config;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\AttributeSetRepository;
use Magento\Framework\Locale\Currency;

/**
 * Class GroupedTest.
 *
 * @covers \Divante\GroupedProductsManager\Ui\DataProvider\Product\Form\Modifier\Grouped::<public>
 */
class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var AttributeRepository|MockObject
     */
    private $eavRepositoryMock;

    /**
     * @var SearchCriteriaBuilderFactory|MockObject
     */
    private $searchCriteriaBuilderFactoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SearchCriteria|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var Config|MockObject
     */
    private $moduleConfigMock;

    /**
     * @var RegistryLocator|MockObject
     */
    private $locatorMock;

    /**
     * @var Url|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Repository|MockObject
     */
    private $productLinkRepositoryMock;

    /**
     * @var ProductRepository
     */
    private $productRepositoryMock;

    /**
     * @var ImageHelper|MockObject
     */
    private $imageHelperMock;

    /**
     * @var Status|MockObject
     */
    private $statusMock;

    /**
     * @var AttributeSetRepository|MockObject
     */
    private $attributeSetRepositoryMock;

    /**
     * @var Currency|MockObject
     */
    private $localeCurrencyMock;

    /**
     * @var Grouped
     */
    private $groupedModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->eavRepositoryMock = $this->getMockBuilder(
            AttributeRepository::class
        )->disableOriginalConstructor()->getMock();

        $this->searchCriteriaBuilderFactoryMock = $this->getMockBuilder(
            SearchCriteriaBuilderFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(
            SearchCriteriaBuilder::class
        )->disableOriginalConstructor()->setMethods(
            [
                'create',
                'addFilter',
            ]
        )->getMock();

        $this->searchCriteriaMock = $this->getMockBuilder(
            SearchCriteria::class
        )->disableOriginalConstructor()->getMock();

        $this->searchCriteriaBuilderFactoryMock->method('create')->willReturn($this->searchCriteriaBuilderMock);
        $this->searchCriteriaBuilderMock->method('create')->willReturn($this->searchCriteriaMock);

        $this->moduleConfigMock = $this->getMockBuilder(
            Config::class
        )->disableOriginalConstructor()->getMock();

        $this->locatorMock = $this->getMockBuilder(
            RegistryLocator::class
        )->disableOriginalConstructor()->getMock();

        $this->urlBuilderMock = $this->getMockBuilder(
            Url::class
        )->disableOriginalConstructor()->getMock();

        $this->productLinkRepositoryMock = $this->getMockBuilder(
            Repository::class
        )->disableOriginalConstructor()->getMock();

        $this->productRepositoryMock = $this->getMockBuilder(
            ProductRepository::class
        )->disableOriginalConstructor()->getMock();

        $this->imageHelperMock = $this->getMockBuilder(
            ImageHelper::class
        )->disableOriginalConstructor()->getMock();

        $this->statusMock = $this->getMockBuilder(
            Status::class
        )->disableOriginalConstructor()->getMock();

        $this->attributeSetRepositoryMock = $this->getMockBuilder(
            AttributeSetRepository::class
        )->disableOriginalConstructor()->getMock();

        $this->localeCurrencyMock = $this->getMockBuilder(
            Currency::class
        )->disableOriginalConstructor()->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->groupedModel = $this->objectManagerHelper->getObject(
            Grouped::class,
            [
                'locator' => $this->locatorMock,
                'urlBuilder' => $this->urlBuilderMock,
                'productLinkRepository' => $this->productLinkRepositoryMock,
                'productRepository' => $this->productRepositoryMock,
                'imageHelper' => $this->imageHelperMock,
                'status' => $this->statusMock,
                'attributeSetRepository' => $this->attributeSetRepositoryMock,
                'localeCurrency' => $this->localeCurrencyMock,
                'eavRepository' => $this->eavRepositoryMock,
                'searchCriteriaBuilderFactory' => $this->searchCriteriaBuilderFactoryMock,
                'moduleConfig' => $this->moduleConfigMock,
                'uiComponentsConfig' => [],
            ]
        );
    }

    /**
     * test static array_insert_before() method.
     */
    public function testArrayInsertBefore()
    {
        $arr = [
            'a' => 1,
            'b' => 2,
            'd' => 4,
        ];

        $result = ($this->groupedModel)::array_insert_before($arr, 'd', ['c' => 3]);

        $this->assertCount(4, $result);
        $this->assertEquals(
            [
                'a',
                'b',
                'c',
                'd',
            ],
            array_keys($result)
        );
    }

    /**
     * test getVisibleProductAttributes() method.
     */
    public function testGetVisibleProductAttributes()
    {
        $expectedNumberOfAttributes = 4;

        $attributesCollection = $this->objectManagerHelper->getCollectionMock(
            '\Magento\Eav\Model\ResourceModel\Attribute\Collection',
            $this->getAttributeMocks($expectedNumberOfAttributes)
        );

        $attributesCollection->method('getItems')->willReturnSelf();

        $this->searchCriteriaBuilderFactoryMock->expects($this->once())->method('create');

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('entity_type_id', 4, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())->method('create');

        $this->eavRepositoryMock->expects($this->once())->method('getList')
            ->with('catalog_product', $this->searchCriteriaMock)
            ->willReturn($attributesCollection);


        $this->assertCount($expectedNumberOfAttributes, $this->groupedModel->getVisibleProductAttributes());
    }

    /**
     * Get array with specified number of Magento_Eav attributes.
     *
     * @param int $limit
     *
     * @return array
     */
    private function getAttributeMocks($limit)
    {
        $data = [];

        for ($i = 0; $i < $limit; $i++) {
            $attribute = $this->getMockBuilder(
                Attribute::class
            )->disableOriginalConstructor()->setMethods(
                [
                    'getIsVisibleOnFront',
                    'getStoreLabel',
                    'getId',
                ]
            )->getMock();

            $attribute->label = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
            $attribute->id = $i;

            $attribute->method('getIsVisibleOnFront')->willReturn(true);
            $attribute->method('getStoreLabel')->willReturn($attribute->label);
            $attribute->method('getId')->willReturn($attribute->id);

            $data[] = $attribute;
        }

        return $data;
    }
}