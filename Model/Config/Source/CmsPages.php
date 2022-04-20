<?php

namespace Magebit\PageListWidget\Model\Config\Source;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class used to get cms pages options
 */
class CmsPages implements OptionSourceInterface
{

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    public const PAGE_VALUE = 'value';
    public const PAGE_LABEL = 'label';

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder   $searchCriteriaBuilder,
        FilterBuilder           $filterBuilder
    )
    {
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * used by widget xml as source of the options for the widget
     */
    public function toOptionArray(): array
    {
        return $this->getPages();
    }

    /**
     * used to get a selected set of cms pages
     */
    public function getPages(array $selectedPages = []): array
    {
        $pages = $this->getCmsPageCollection($selectedPages);
        return $this->formatPages($pages);
    }

    /**
     * formats cms pages data. Keeps page title and identifier only
     */
    private function formatPages(array $pages): array
    {
        $optionArray = [];
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
    private function getCmsPageCollection(array $selectedPages): array
    {
        $filters = [];
        foreach ($selectedPages as $selectedPage) {
            $filters[] = $this->filterBuilder->setField('identifier')->setConditionType('eq')->setValue($selectedPage)->create();
        }
        $searchCriteria = $this->searchCriteriaBuilder->addFilters($filters)->create();

        try {
            return $this->pageRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

}
