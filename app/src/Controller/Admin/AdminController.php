<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\DashboardBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @Route("/secure")
 *
 * @IsGranted("ROLE_CONSULTANT")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="htcintranet_admin_dashboard")
     */
    public function adminDashboard(DashboardBuilder $dashboardBuilder): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('dashboard/index.html.twig', [
            'dashboard_data' => $dashboardBuilder->getData(),
        ]);
    }
}
