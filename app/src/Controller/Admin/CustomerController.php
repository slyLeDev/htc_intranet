<?php
/**
 * @author hR.
 */

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Service\CustomerFileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/secure/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/{id?}", name="htcintranet_customer_index", methods={"GET", "POST"})
     */
    public function index(
        ?Customer $customer,
        Request $request,
        CustomerRepository $customerRepository,
        CustomerFileUploader $customerFileUploader
    ): Response
    {
        $customer = $customer ?? new Customer();
        $oldFilename = $customer->getLogo();
        $theId = $customer->getId() ?? $customerRepository->getNextId();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();

            if ($logoFile) {
                $customer->setLogo($customerFileUploader->upload($logoFile, $oldFilename));
            }

            $customerRepository->add($customer, true);
            $this->addFlash('success', 'Votre modification a bien été enregistré');

            return $this->redirectToRoute('htcintranet_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/index.html.twig', [
            'admin_list_customer' => true,
            'the_id' => $theId,
            'is_new' => !((bool) $customer->getId()),
            'customers' => $customerRepository->findAll(),
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sheet/{id}", name="htcintranet_customer_sheet", methods={"GET"}, options={"expose"=true})
     */
    public function sheet(Customer $customer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json([
            'success' => true,
            'sheet' => $this->renderView('customer/customer_sheet.html.twig', [
                'customer' => $customer,
            ]),
        ]);
    }
}
