<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\DealRepository;
use App\Service\DealFileUploader;
use App\Service\DealReferenceBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("secure/deal")
 */
class DealController extends AbstractController
{
    /**
     * @Route("/", name="htcintranet_deal_index", methods={"GET"})
     */
    public function index(?Deal $deal, Request $request, DealRepository $dealRepository): Response
    {
        return $this->render('deal/index.html.twig', [
            'admin_list_deal' => true,
            'deals' => $dealRepository->findAll(),
        ]);
    }

    /**
     * @Route("/manage/{id?}", name="htcintranet_deal_manage", methods={"GET", "POST"})
     */
    public function manage(
        ?Deal $deal,
        Request $request,
        DealRepository $dealRepository,
        DealFileUploader $dealFileUploader,
        DealReferenceBuilder $dealReferenceBuilder
    ): Response
    {
        $deal = $deal ?? new Deal();
        $theId = $deal->getId() ?? $dealRepository->getNextId();
        if (!$deal->getId()) {
            $deal->setReference($dealReferenceBuilder->generate());
        }
        $oldFilename = $deal->getDealFilename();

        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $dealFile */
            $dealFile = $form->get('dealFile')->getData();

            if ($dealFile) {
                $deal->setDealFilename($dealFileUploader->upload($dealFile, $oldFilename));
            }

            $dealRepository->add($deal, true);
            $this->addFlash('success', 'Votre modification a bien été enregistré');

            return $this->redirectToRoute('htcintranet_deal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deal/manage.html.twig', [
            'admin_list_deal' => true,
            'the_id' => $theId,
            'is_new' => !((bool) $deal->getId()),
            'deal' => $deal,
            'form' => $form,
            'is_viewable_file' => $deal->isViewableFile($this->getParameter('kernel.project_dir').'/public/uploads/deal_file/'),
            'viewable_filename' => $deal->getViewableFilename(),
        ]);
    }

    /**
     * @Route("/sheet/{id}", name="htcintranet_deal_sheet", methods={"GET"}, options={"expose"=true})
     */
    public function sheet(Deal $deal): JsonResponse
    {
        return $this->json([
            'success' => true,
            'sheet' => $this->renderView('deal/deal_sheet.html.twig', [
                'deal' => $deal,
                'is_viewable_file' => $deal->isViewableFile($this->getParameter('kernel.project_dir').'/public/uploads/deal_file/'),
                'viewable_filename' => $deal->getViewableFilename(),
            ]),
        ]);
    }
}
