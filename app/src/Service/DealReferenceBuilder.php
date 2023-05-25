<?php
/**
 * @author hR.
 */

namespace App\Service;

use App\Repository\DealRepository;

class DealReferenceBuilder
{
    private DealRepository $dealRepository;

    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    public function generate(?string $forceSuffix = ''): string
    {
        if ($forceSuffix) {
            return 'C_'.$forceSuffix;
        }

        $nextId = $this->dealRepository->getNextId();

        return 'C_'.$nextId.(date('mY'));
    }
}