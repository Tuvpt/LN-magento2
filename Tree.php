<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Block\Adminhtml\CategoryPage;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Tree as TreeResource;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DB\Helper;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

/**
 * @method Tree setUseAjax($useAjax)
 * @method bool getUseAjax()
 */
class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{

    /**
     * Tree constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        TreeResource $categoryTree,
        Registry $registry,
        CatalogCategoryFactory $categoryFactory,
        EncoderInterface $jsonEncoder,
        Helper $resourceHelper,
        Session $backendSession,
        array $data = []
    )
    {
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $jsonEncoder, $resourceHelper, $backendSession, $data);

        $this->_withProductCount = false;
    }

    /**
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('mplayer/categorypage/jsonTree');
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('mplayer/categorypage/move');
    }

    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        $params = ['_current' => false, '_query' => false];
        $params = array_merge($params, $args);

        return $this->getUrl('mplayer/*/save', $params);
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            'mplayer/categorypage/edit',
            ['store' => null, '_query' => false, 'id' => null, 'parent' => null]
        );
    }


}
