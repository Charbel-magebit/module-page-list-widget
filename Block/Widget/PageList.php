<?php declare(strict_types=1);

namespace Magebit\PageListWidget\Block\Widget;

use Magebit\PageListWidget\Model\Config\Source\CmsPages;
use Magento\Framework\UrlInterface;
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

    /** @var UrlInterface $urlBuilder */
    public $urlBuilder;

    /**
     * @var CmsPages $cmsPages
     */
    private $cmsPages;

    public function __construct(
        Context      $context,
        CmsPages     $cmsPages,
        UrlInterface $urlBuilder,
        array        $data = []
    )
    {
        parent::__construct($context, $data);
        $this->cmsPages = $cmsPages;
        $this->urlBuilder = $urlBuilder;
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

        if ($displayMode === self::DISPLAY_MODE_ALL_PAGES) {
            return $this->cmsPages->getPages();
        }

        $selectedPageValues = explode(',', $this->getData(self::PARAMETER_SELECTED_PAGES));
        return $this->cmsPages->getPages($selectedPageValues);
    }
}