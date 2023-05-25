<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\JobSector;
use App\Form\JobSectorType;
use App\Repository\JobSectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secure/job/sector")
 */
class JobSectorController extends AbstractController
{
    /**
     * @Route("/{id?}", name="htcintranet_job_sector_index", methods={"GET", "POST"})
     */
    public function index(?JobSector $jobSector, Request $request, JobSectorRepository $jobSectorRepository): Response
    {
        $jobSector = $jobSector ?? new JobSector();
        $theId = $jobSector->getId() ?? $jobSectorRepository->getNextId();
        $form = $this->createForm(JobSectorType::class, $jobSector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobSectorRepository->add($jobSector, true);

            return $this->redirectToRoute('htcintranet_job_sector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_sector/index.html.twig', [
            'admin_list_job_sector' => true,
            'the_id' => $theId,
            'is_new' => !((bool) $jobSector->getId()),
            'job_sectors' => $jobSectorRepository->findAll(),
            'job_sector' => $jobSector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="htcintranet_job_sector_new", methods={"GET", "POST"})
     */
    public function new(Request $request, JobSectorRepository $jobSectorRepository): Response
    {
        $jobSector = new JobSector();
        $form = $this->createForm(JobSectorType::class, $jobSector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobSectorRepository->add($jobSector, true);

            return $this->redirectToRoute('htcintranet_job_sector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_sector/new.html.twig', [
            'job_sector' => $jobSector,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="htcintranet_job_sector_show", methods={"GET"})
     */
    public function show(JobSector $jobSector): Response
    {
        return $this->render('job_sector/show.html.twig', [
            'job_sector' => $jobSector,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="htcintranet_job_sector_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, JobSector $jobSector, JobSectorRepository $jobSectorRepository): Response
    {
        $form = $this->createForm(JobSectorType::class, $jobSector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobSectorRepository->add($jobSector, true);

            return $this->redirectToRoute('htcintranet_job_sector_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_sector/edit.html.twig', [
            'job_sector' => $jobSector,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="htcintranet_job_sector_delete", methods={"POST"})
     */
    public function delete(Request $request, JobSector $jobSector, JobSectorRepository $jobSectorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobSector->getId(), $request->request->get('_token'))) {
            $jobSectorRepository->remove($jobSector, true);
        }

        return $this->redirectToRoute('htcintranet_job_sector_index', [], Response::HTTP_SEE_OTHER);
    }
}
