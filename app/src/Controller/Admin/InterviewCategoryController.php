<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\InterviewCategory;
use App\Entity\JobSector;
use App\Form\InterviewCategoryType;
use App\Form\JobSectorType;
use App\Repository\InterviewCategoryRepository;
use App\Repository\JobSectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secure/interview/category")
 */
class InterviewCategoryController extends AbstractController
{
    /**
     * @Route("/{id?}", name="htcintranet_interview_category_index", methods={"GET", "POST"})
     */
    public function index(?InterviewCategory $interviewCategory, Request $request, InterviewCategoryRepository $interviewCategoryRepository): Response
    {
        $itwCategory = $interviewCategory ?? new InterviewCategory();
        $theId = $itwCategory->getId() ?? $interviewCategoryRepository->getNextId();
        $form = $this->createForm(InterviewCategoryType::class, $itwCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $interviewCategoryRepository->add($itwCategory, true);

            return $this->redirectToRoute('htcintranet_interview_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('interview/category/index.html.twig', [
            'admin_list_interview_category' => true,
            'the_id' => $theId,
            'is_new' => !((bool) $itwCategory->getId()),
            'itw_categories' => $interviewCategoryRepository->findAll(),
            'itw_category' => $itwCategory,
            'form' => $form->createView(),
        ]);
    }
}
