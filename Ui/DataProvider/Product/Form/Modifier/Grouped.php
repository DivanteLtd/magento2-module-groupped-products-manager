<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\Grouped as MagentoGrouped;
use Magento\Ui\Component\Form;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Divante\GroupedProductsManager\Helper\Config;

/**
 * Class Grouped.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grouped extends MagentoGrouped
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $eavRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * Grouped constructor.
     *
     * @param LocatorInterface                $locator
     * @param UrlInterface                    $urlBuilder
     * @param ProductLinkRepositoryInterface  $productLinkRepository
     * @param ProductRepositoryInterface      $productRepository
     * @param ImageHelper                     $imageHelper
     * @param Status                          $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param CurrencyInterface               $localeCurrency
     * @param AttributeRepositoryInterface    $eavRepository
     * @param SearchCriteriaBuilderFactory    $searchCriteriaBuilderFactory
     * @param Config                          $moduleConfig
     * @param array                           $uiComponentsConfig
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        CurrencyInterface $localeCurrency,
        AttributeRepositoryInterface $eavRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Config $moduleConfig,
        array $uiComponentsConfig = []
    ) {
        $this->eavRepository                = $eavRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->moduleConfig                 = $moduleConfig;

        parent::__construct($locator, $urlBuilder, $productLinkRepository, $productRepository, $imageHelper, $status,
            $attributeSetRepository, $localeCurrency, $uiComponentsConfig);
    }

    /**
     * Insert a value or key/value pair before a specific key in an array.  If key doesn't exist, value is prepended
     * to the beginning of the array.
     *
     * @param array  $array
     * @param string $key
     * @param array  $new
     *
     * @return array
     */
    public static function array_insert_before(array $array, $key, array $new)
    {
        $keys = array_keys($array);
        $pos  = (int)array_search($key, $keys);

        return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }

    /**
     * {@inheritdoc}
     */
    protected function fillMeta()
    {
        $result = parent::fillMeta();

        if (!$this->moduleConfig->isEnabledAttributesVisibility()) {
            return $result;
        }

        $visibleAttribute = [
            'visible_attributes' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType'          => Form\Element\DataType\Number::NAME,
                            'formElement'       => Form\Element\MultiSelect::NAME,
                            'componentType'     => Form\Field::NAME,
                            'dataScope'         => 'visible_attributes',
                            'label'             => __('Visible attributes'),
                            'fit'               => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder'         => 85,
                            'validation'        => [],
                            'options'           => $this->getVisibleProductAttributes(),
                        ],
                    ],
                ],
            ],
        ];

        $result = self::array_insert_before($result, 'actionDelete', $visibleAttribute);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function fillData(ProductInterface $linkedProduct, ProductLinkInterface $linkItem)
    {
        $result = parent::fillData($linkedProduct, $linkItem);

        if (!$this->moduleConfig->isEnabledAttributesVisibility()) {
            return $result;
        }

        $baseProduct                     = $this->productRepository->get($linkItem->getSku());
        $visibleAttributesForAllProducts = unserialize($baseProduct->getProductsAttributesVisibility());

        if (isset($visibleAttributesForAllProducts[$linkedProduct->getId()])) {
            $result['visible_attributes'] = explode(',', $visibleAttributesForAllProducts[$linkedProduct->getId()]);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getVisibleProductAttributes()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter('entity_type_id', 4, 'eq')->create();

        $attributes = $this->eavRepository->getList('catalog_product', $searchCriteriaBuilder->create());

        $optionAttributes = [];

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($attributes->getItems() as $attribute) {
            $optionAttributes[] = ['label' => $attribute->getStoreLabel(), 'value' => $attribute->getId()];
        }

        return $optionAttributes;
    }
}
