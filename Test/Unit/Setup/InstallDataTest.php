<?php
/**
 * @package   Divante\GroupedProductsManager
 * @author    Marek Mularczyk <mmularczyk@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\GroupedProductsManager\Test\Unit\Setup;

use Divante\GroupedProductsManager\Setup\InstallData;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Setup\Module\DataSetup;
use Magento\Setup\Model\ModuleContext;

/**
 * Class InstallDataTest.
 */
class InstallDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var EavSetupFactory|MockObject
     */
    private $eavSetupFactoryMock;

    /**
     * @var EavSetup|MockObject
     */
    private $eavSetupMock;

    /**
     * @var DataSetup
     */
    private $setupMock;

    /**
     * @var ModuleContext
     */
    private $contextMock;

    /**
     * @var InstallData
     */
    private $installDataModel;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->eavSetupFactoryMock = $this->getMockBuilder(
            EavSetupFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->eavSetupMock = $this->getMockBuilder(
            EavSetup::class
        )->disableOriginalConstructor()->setMethods(['addAttribute'])->getMock();

        $this->eavSetupFactoryMock->method('create')->willReturn($this->eavSetupMock);

        $this->setupMock = $this->getMockBuilder(DataSetup::class)->disableOriginalConstructor()->getMock();
        $this->contextMock = $this->getMockBuilder(ModuleContext::class)->disableOriginalConstructor()->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->installDataModel = $this->objectManagerHelper->getObject(
            InstallData::class,
            ['eavSetupFactory' => $this->eavSetupFactoryMock]
        );
    }

    /**
     * test install() method.
     */
    public function testInstall()
    {
        $this->eavSetupFactoryMock->expects($this->once())->method('create')->with(['setup' => $this->setupMock]);
        $this->eavSetupMock->expects($this->once())->method('addAttribute');

        $this->installDataModel->install($this->setupMock, $this->contextMock);
    }
}
