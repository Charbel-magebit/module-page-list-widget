<?php

namespace Magebit\PageListWidget\Model\Config\Source;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class used to get cms pages options
 */
class CmsPages implements OptionSourceInterface
{

    /**
     * @var PageRepositoryInterface
     */
    private  $_pageRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private  $_searchCriteriaBuilder;

    public const PAGE_VALUE = 'value';
    public const PAGE_LABEL = 'label';

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ){
        $this->_pageRepository= $pageRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * formats cms pages data. Keeps page title and identifier only
     */
    public function toOptionArray(): array
    {
        $optionArray = [];
        $pages = $this->getCmsPageCollection();
        foreach ($pages as $page) {
            $optionArray[] = [
                self::PAGE_VALUE => $page->getIdentifier(),
                self::PAGE_LABEL => $page->getTitle()
            ];
        }

        return $optionArray;
    }

    /**
     * Gets all cms pages data from page repository
     * if error occurs returns empty array
     */
    private function getCmsPageCollection(): array
    {
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        try {
            return $this->_pageRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

}
