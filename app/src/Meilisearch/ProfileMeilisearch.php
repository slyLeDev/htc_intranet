<?php
/**
 * @author hR.
 */

namespace App\Meilisearch;

use App\Entity\Profile;
use App\Interfaces\MeilisearchInterface;
use MeiliSearch\Bundle\Collection;

/**
 * ProfileMeilisearch
 */
class ProfileMeilisearch extends AbstractMeilisearch implements MeilisearchInterface
{
    function getEntity(): string
    {
        return Profile::class;
    }

    function doSearch(string $searchTerm, array $searchParams = []): array
    {
        $start = microtime(true);
        $resultSearch = $this->searchService->search($this->entityManager, $this->getEntity(), $searchTerm, $searchParams);
        $end = microtime(true);
        return [
            'resultSearch' => $resultSearch,
            'raw' => $this->searchService->rawSearch($this->getEntity(), $searchTerm, $searchParams),
            'perf' => number_format(($end - $start), 4),
        ];
    }

    function doRawSearch(string $searchTerm, array $searchParams = []): array
    {
        return $this->searchService->rawSearch($this->getEntity(), $searchTerm, $searchParams);
    }

    function getConfiguration(): Collection
    {
        return $this->searchService->getConfiguration();
    }
}