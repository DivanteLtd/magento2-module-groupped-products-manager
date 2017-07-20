<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Plugin\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Builder as MagentoBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Divante\GroupedProductsManager\Helper\Config;

/**
 * Class BuilderPlugin.
 */
class BuilderPlugin
{
    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * BuilderPlugin constructor.
     *
     * @param Config $moduleConfig
     */
    public function __construct(Config $moduleConfig)
    {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @param MagentoBuilder   $subject
     * @param callable         $proceed
     * @param RequestInterface $request
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function aroundBuild(MagentoBuilder $subject, callable $proceed, RequestInterface $request)
    {
        $product           = $proceed($request);
        $data              = $request->getParam('links');
        $visibleAttributes = [];

        if ($this->moduleConfig->isEnabledAttributesVisibility()
            && $product->getTypeId() === Grouped::TYPE_CODE
            && isset($data['associated'])
        ) {
            foreach ($data['associated'] as $productItem) {
                if (isset($productItem['visible_attributes'])) {
                    $visibleAttributes[$productItem['id']] = implode(',', $productItem['visible_attributes']);
                }
            }

            $product->setProductsAttributesVisibility(serialize($visibleAttributes));
        }

        return $product;
    }
}
