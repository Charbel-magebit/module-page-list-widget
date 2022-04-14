<?php declare(strict_types=1);

namespace Magebit\PageListWidget\Block\Widget;

use Magebit\PageListWidget\Model\Config\Source\CmsPages;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class responsible for controlling the page list widget
 */
class PageList extends Template implements BlockInterface
{
    /**
     * Name of the template responsible for rendering the widget html
     */
    protected $_template = "widget/page-list.phtml";

    /**
     * parameters that a user sets when creating a widget.
     * these need to stay in sync with the parameters declared in widget.xml
     */
    public const PARAMETER_TITLE = 'title';
    public const PARAMETER_DISPLAY_MODE = 'display_mode';
    public const PARAMETER_SELECTED_PAGES = 'selected_pages';

    /**
     * Possible values display mode parameter can take
     */
    public const DISPLAY_MODE_ALL_PAGES = 'all_pages';
    public const DISPLAY_MODE_SPECIFIC_PAGES = 'specific_pages';

    /**
     * Source of cms pages
     * @var CmsPages $_cmsPages
     */
    private $_cmsPages;

    public function __construct(Context $context, CmsPages $cmsPages, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_cmsPages = $cmsPages;
    }

    /**
     * Returns an array with cms pages depending on the chosen display mode.
     * if display mode is:
     * ---> all pages then returns all cms pages
     * ---> specific pages then returns the cms pages selected by the user when creating the widget
     */
    public function getCmsPages(): array
    {
        $displayMode = $this->getData(self::PARAMETER_DISPLAY_MODE);
        $cmsPages = $this->_cmsPages->toOptionArray();

        if ($displayMode === self::DISPLAY_MODE_ALL_PAGES) {
            return $cmsPages;
        }

        $selectedPageValues = explode(",", $this->getData(self::PARAMETER_SELECTED_PAGES));

        return array_filter($cmsPages, function ($page) use ($selectedPageValues) {
            return in_array($page[CmsPages::PAGE_VALUE], $selectedPageValues);
        });
    }
}