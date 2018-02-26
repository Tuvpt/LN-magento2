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

namespace Mageplaza\LayeredNavigationUltimate\Block\Adminhtml\CategoryPage\Edit;

use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class Form
 * @package Mageplaza\Blog\Block\Adminhtml\Category\Edit
 */
class Form extends AbstractCategory
{
    /**
     * Additional buttons
     *
     * @var array
     */
    public $additionalButtons = [];

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'categorypage/edit/form.phtml';

    /**
     * JSON encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Mageplaza\Blog\Model\ResourceModel\Category\Tree $blogCategoryTree
     * @param \Mageplaza\Blog\Model\CategoryFactory $blogCategoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        EncoderInterface $jsonEncoder,
        array $data = []
    )
    {
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);

        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryId = (int)$category->getId(); // 0 when we create Blog Category, otherwise some value for editing Blog Category

        // Save button
        $this->addButton(
            'save',
            [
                'id' => 'save',
                'label' => __('Save Category'),
                'class' => 'save primary save-category',
                'data_attribute' => [
                    'mage-init' => [
                        'mplayer/categorypage/edit' => [
                            'url' => $this->getSaveUrl(),
                            'ajax' => true
                        ]
                    ]
                ]
            ]
        );

        // Reset button
        $resetPath = $categoryId ? 'mplayer/*/edit' : 'mplayer/*/add';
        $this->addButton(
            'reset',
            [
                'id' => 'reset',
                'label' => __('Reset'),
                'onclick' => "categoryReset('" . $this->getUrl($resetPath, ['_current' => true]) . "',false)",
                'class' => 'reset'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function isAjax()
    {
        return $this->getRequest()->isAjax();
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
        return $this->getUrl('mplayer/categorypage/edit', ['_query' => false, 'id' => null, 'parent' => null]);
    }

    /**
     * @param $alias
     * @param $config
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        if ($this->hasToolbarBlock()) {
            $this->addButton($alias, $config);
        } else {
            $this->setChild(
                $alias . '_button',
                $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->addData($config)
            );
            $this->additionalButtons[$alias] = $alias . '_button';
        }

        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return $this
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->additionalButtons[$alias])) {
            $this->unsetChild($this->additionalButtons[$alias]);
            unset($this->additionalButtons[$alias]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }



    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);

        return $this->getUrl('mplayer/*/refreshPath', $params);
    }

    /**
     * Get parent Blog Category id
     *
     * @return int
     */
    public function getParentCategoryId()
    {
        return (int)$this->templateContext->getRequest()->getParam('parent');
    }

    /**
     * Get Blog Category  id
     *
     * @return int
     */
    public function getCategoryId()
    {
        return (int)$this->templateContext->getRequest()->getParam('id');
    }

    /**
     * @param $buttonId
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addButton($buttonId, array $data)
    {
        $childBlockId = $buttonId . '_button';
        $button = $this->getButtonChildBlock($childBlockId);
        $button->setData($data);
        $block = $this->getLayout()->getBlock('page.actions.toolbar');
        if ($block) {
            $block->setChild($childBlockId, $button);
        } else {
            $this->setChild($childBlockId, $button);
        }
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasToolbarBlock()
    {
        return $this->getLayout()->isBlock('page.actions.toolbar');
    }

    /**
     * @param $childId
     * @param null $blockClassName
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonChildBlock($childId, $blockClassName = null)
    {
        if (null === $blockClassName) {
            $blockClassName = 'Magento\Backend\Block\Widget\Button';
        }

        return $this->getLayout()->createBlock($blockClassName, $this->getNameInLayout() . '-' . $childId);
    }

    /**
     * @return string
     */
    public function getPostsJson()
    {
        $posts = $this->getCategory()->getPostsPosition();
        if (!empty($posts)) {
            return $this->jsonEncoder->encode($posts);
        }

        return '{}';
    }
}
