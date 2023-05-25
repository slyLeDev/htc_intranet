<?php
/**
 * @author hR.
 */

namespace App\Meilisearch;

use App\Interfaces\MeilisearchInterface;
use Doctrine\ORM\EntityManagerInterface;
use MeiliSearch\Bundle\Collection;
use MeiliSearch\Bundle\SearchService;

abstract class AbstractMeilisearch
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SearchService          $searchService
     */
    public function __construct(EntityManagerInterface $entityManager, SearchService $searchService)
    {
        $this->entityManager = $entityManager;
        $this->searchService = $searchService;
    }

    protected function doIndex(object $entityObject)
    {
        $this->searchService->index($this->entityManager, $entityObject);
    }
}