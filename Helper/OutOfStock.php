<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Catalog\Model\Product;

/**
 * Class OutOfStock.
 */
class OutOfStock extends AbstractHelper
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * OutOfStock constructor.
     *
     * @param Context   $context
     * @param UrlHelper $urlHelper
     */
    public function __construct(Context $context, UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;

        parent::__construct($context);
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getProductAlertUrl($product)
    {
        return $this->_getUrl(
            'productalert/add/stock',
            [
                'product_id'                            => $product->getId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl(),
            ]
        );
    }
}
