<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Manager\ProfileManager;
use App\Meilisearch\ProfileMeilisearch;
use App\Repository\CustomerRepository;
use App\Repository\JobSectorRepository;
use App\Repository\ProfileRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secure/profile", name="htcintranet_admin_profile_")
 *
 * @IsGranted("ROLE_CONSULTANT")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/search", name="search", methods={"POST"}, options={"expose"=true})
     */
    public function search(Request $request, ProfileManager $profileManager): JsonResponse
    {
        return $this->json($profileManager->handleSearchRequest($request));
    }

    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(CustomerRepository $customerRepository, JobSectorRepository $jobSectorRepository): Response
    {
        return $this->render('profile/list.html.twig', [
            'admin_list_profile' => true,
            'customers' => $customerRepository->findAll(),
            'sectors' => $jobSectorRepository->getAllDistinct(),
        ]);
    }

    /**
     * @Route("/received", name="received", methods={"GET"})
     */
    public function received(JobSectorRepository $jobSectorRepository): Response
    {
        return $this->render('profile/received.html.twig', [
            'admin_list_profile_received' => true,
            'is_received' => true,
            'sectors' => $jobSectorRepository->getAllDistinct(),
        ]);
    }

    /**
     * @Route("/new", name="create", methods={"GET", "POST"})
     */
    public function new(Request $request, ProfileRepository $profileRepository): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('htcintranet_admin_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Profile $profile): Response
    {
        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Profile $profile, ProfileRepository $profileRepository): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('htcintranet_admin_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/sheet/{id}", name="sheet", methods={"GET"}, options={"expose"=true})
     */
    public function sheet(Profile $profile): JsonResponse
    {
        return $this->json([
            'success' => true,
            'sheet' => $this->renderView('profile/profile_sheet.html.twig', [
                'profile' => $profile,
            ]),
        ]);
    }
}
