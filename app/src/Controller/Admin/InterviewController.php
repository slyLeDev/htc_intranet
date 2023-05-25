<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\Interview;
use App\Entity\InterviewCategory;
use App\Entity\JobSector;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\InterviewCategoryType;
use App\Form\InterviewType;
use App\Form\JobSectorType;
use App\Repository\InterviewCategoryRepository;
use App\Repository\InterviewRepository;
use App\Repository\JobSectorRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secure/interview")
 */
class InterviewController extends AbstractController
{
    /**
     * @Route("/", name="htcintranet_interview_index", methods={"GET", "POST"})
     *
     * @throws NonUniqueResultException
     */
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('interview/index.html.twig', [
            'user_consultant' => $userRepository->findByRole(User::ROLE_CONSULTANT),
        ]);
    }

    /**
     * @Route("/form_event/{id?}", name="htcintranet_interview_form_event", methods={"GET", "POST"}, options={"expose"=true})
     *
     */
    public function getFormEvent(?Interview $interview, Request $request, InterviewRepository $interviewRepository): JsonResponse
    {
        $interview = $interview ?? new Interview();
        $user = $this->getUser();
        $isNew = !($interview && $interview->getId());
        $form = $this->createForm(InterviewType::class, $interview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hourStart = $request->request->get('interview')['hourStart'];
            $hourEnd = $request->request->get('interview')['hourEnd'];
            $explodeHourStart = explode(':', $hourStart);
            $explodeHourEnd = explode(':', $hourEnd);

            $dateStart = $interview->getDateStart()->setTime((int) $explodeHourStart[0] ?? 0, (int) $explodeHourStart[1] ?? 0);
            $dateEnd = $interview->getDateEnd()->setTime((int) $explodeHourEnd[0] ?? 0, (int) $explodeHourEnd[1] ?? 0);

            $interviewRepository->add(($interview
                ->setConsultant($user)
                ->setDateStart($dateStart)
                ->setDateEnd($dateEnd))
                ->buildTitle()
            , true);

            //$this->addFlash('success', 'L\'enregistrement a bien été effectué');

            return new JsonResponse([
                'success' => true,
                'event' => $interview->getRenderedData(),
                'is_new' => $isNew,
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('danger', 'Erreur lors de l\'enregistrement');
        }

        $contentForm = $this->renderView('interview/event/_form.html.twig', [
            'form' => $form->createView(),
            'admin_interview_list' => true,
            'interview' => $interview,
            'is_new' => $isNew,
        ]);

        return new JsonResponse([
            'success' => true,
            'content' => $contentForm,
            'is_new' => $isNew,
        ]);
    }

    /**
     * @Route("/fetch_all_event/{id?}", name="htcintranet_interview_fetch_all_event", methods={"GET", "POST"}, options={"expose"=true})
     *
     * @throws Exception
     */
    public function fetchAllEvent(InterviewRepository $interviewRepository): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'events' => $interviewRepository->fetchAllEvent(),
        ]);
    }
}
