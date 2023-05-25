<?php
/**
 * @author hR.
 */
namespace App\Manager;

use App\Entity\Profile;
use App\Meilisearch\ProfileMeilisearch;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/** ProfileManager */
class ProfileManager
{
    /**
     * @var ProfileMeilisearch
     */
    private $profileMeilisearch;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(ProfileMeilisearch $profileMeilisearch, Environment $twig)
    {
        $this->profileMeilisearch = $profileMeilisearch;
        $this->twig = $twig;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function handleSearchRequest(Request $request): array
    {
        $searchTerm = $request->get('querySearch');
        $pageNumber = (int) $request->get('pageNumber');
        $limitPerPage = (int) $request->get('limitPerPage');
        $orderBy = $request->get('orderBy');
        $orderByDirection = $request->get('orderByDirection');
        $xpYear = $request->get('xpYear');
        $justReceived = ('1' === $request->get('justReceived'));
        $status = $justReceived ? Profile::RECEIVED : $request->get('status');
        $sectors = $request->get('sectors');

        $offset = $pageNumber * $limitPerPage;
        $payload = [
            'limit' => $limitPerPage,
            'offset' => $offset
        ];

        if (!$justReceived) {
            $payload['sort'] = [$orderBy.':'.$orderByDirection];
        }

        if (null !== $xpYear && '' !== $xpYear && 'undefined' !== $xpYear) {
            $operator = (Profile::XP_YEAR_TRAINEE === (int) $xpYear) ? ' = ' : ' >= ';
            $payload['filter'] = 'xpYear'.$operator.$xpYear;
            if (Profile::XP_YEAR_BEGINNER === (int) $xpYear) {
                $payload['filter'] = 'xpYear >= 0 AND xpYear <= 1';
            }
        }

        if (null !== $status && '' !== $status && 'undefined' !== $status) {
            if (isset($payload['filter'])) {
                $payload['filter'] .= " AND status = '$status'";
            } else {
                $payload['filter'] = "status = '$status'";
            }
        }
        if (null !== $sectors && '' !== $sectors && 'undefined' !== $sectors) {
            if (isset($payload['filter'])) {
                $payload['filter'] .= " AND sectors.name = '$sectors'";
            } else {
                $payload['filter'] = "sectors.name = '$sectors'";
            }
        }
        //dd($payload);
        $resultSearch = $this->profileMeilisearch->doSearch($searchTerm, $payload);
        $totalPage = ($resultSearch['raw']['nbHits'] > 0) ? ceil(($resultSearch['raw']['nbHits'] / $resultSearch['raw']['limit'])) : 0;

        return [
            'resultSearch' => $this->twig->render('profile/profile_card_item.html.twig', [
                'profiles' => $resultSearch['resultSearch'],
            ]),
            'perf' => $resultSearch['perf'],
            'count' => $resultSearch['raw']['nbHits'],
            'pageNumber' => $pageNumber,
            'totalPage' => $totalPage,
            'querySearch' => $searchTerm,
            'showPreviousButton' => $offset >= $resultSearch['raw']['limit'],
            'showNextButton' => $pageNumber < (int) ($resultSearch['raw']['nbHits'] / $resultSearch['raw']['limit']),
        ];
    }
}
