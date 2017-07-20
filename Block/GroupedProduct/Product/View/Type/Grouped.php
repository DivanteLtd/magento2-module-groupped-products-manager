<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Block\GroupedProduct\Product\View\Type;

use Divante\GroupedProductsManager\Helper\OutOfStock;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\GroupedProduct\Block\Product\View\Type\Grouped as MagentoGrouped;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Catalog\Helper\Output;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\ProductFactory;
use Divante\GroupedProductsManager\Helper\Config;

/**
 * Class Grouped.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grouped extends MagentoGrouped
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * @var OutOfStock
     */
    private $outOfStockHelper;

    /**
     * Grouped constructor.
     *
     * @param SearchCriteriaBuilderFactory        $searchCriteriaBuilderFactory
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param Output                              $outputHelper
     * @param PriceCurrencyInterface              $priceCurrency
     * @param ProductFactory                      $productFactory
     * @param OutOfStock                          $outOfStockHelper
     * @param Config                              $moduleConfig
     * @param Context                             $context
     * @param ArrayUtils                          $arrayUtils
     * @param array                               $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        Output $outputHelper,
        PriceCurrencyInterface $priceCurrency,
        ProductFactory $productFactory,
        OutOfStock $outOfStockHelper,
        Config $moduleConfig,
        Context $context,
        ArrayUtils $arrayUtils,
        array $data = []
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->productAttributeRepository   = $productAttributeRepository;
        $this->outputHelper                 = $outputHelper;
        $this->priceCurrency                = $priceCurrency;
        $this->productFactory               = $productFactory;
        $this->moduleConfig                 = $moduleConfig;
        $this->outOfStockHelper             = $outOfStockHelper;

        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @param Product $product
     * @param Product $item
     *
     * @return array
     */
    public function resolveVisibleAttributes($product, $item)
    {
        /** @var Product $productModel */
        $productModel = $this->productFactory->create();
        $item->getResource()->load($productModel, $item->getId());

        $output            = [];
        $visibleAttributes = unserialize($product->getProductsAttributesVisibility());

        if (!isset($visibleAttributes[$item->getId()])) {
            return $output;
        }

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter('attribute_id', explode(',', $visibleAttributes[$item->getId()]), 'in');

        $attributes = $this->productAttributeRepository->getList($searchCriteriaBuilder->create())->getItems();

        foreach ($attributes as $attribute) {
            $value = $attribute->getFrontend()->getValue($productModel);

            if (!$productModel->hasData($attribute->getAttributeCode())) {
                $value = __('N/A');
            } elseif ((string)$value == '') {
                $value = __('No');
            } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                $value = $this->priceCurrency->convertAndFormat($value);
            }

            $output[$attribute->getAttributeCode()] = [
                'label' => __($attribute->getStoreLabel()),
                'value' => $value,
            ];
        };

        return $output;
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getProductStockAlertUrl($product)
    {
        return $this->outOfStockHelper->getProductAlertUrl($product);
    }

    /**
     * @return Output
     */
    public function getOutputHelper()
    {
        return $this->outputHelper;
    }

    /**
     * @return Config
     */
    public function getModuleConfigHelper()
    {
        return $this->moduleConfig;
    }
}
