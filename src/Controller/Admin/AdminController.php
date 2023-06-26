<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{

    // #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
    public function addProduct(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response|RedirectResponse
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // traitement de l'image envoyée
            $photo = $form->get('photo')->getData();
            // dd($photo);
            if($photo) {
                $originalFileName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' .$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('product_directory'), $newFileName
                    );
                } catch (FileException $e){
                    die();
                }
    
                $produit->setPhoto($newFileName);

            } else {
                $produit->setPhoto($this->getParameter('generic_product_img'));
            }

            $db = $doctrine->getManager();
            $db->persist($produit);
            $db->flush();

            $this->addFlash('success', 'Le produit a été ajouté avec succès.');
            return $this->redirectToRoute('app_read_products');
            
        }

        return $this->render('admin/add-product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function readProducts(Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator): Response {
        $db = $doctrine->getManager();
        // $produits = $db->getRepository(Produit::class)->findAll();

        $pagination = $paginator->paginate(
            $db->getRepository(Produit::class)->findAll(),
            $request->query->getInt('page', 1),
            5
        );



        return $this->render('admin/read-products.html.twig',[
            "produits" => $pagination,
        ]);
    }

    public function updateProduct(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, int $id): Response|RedirectResponse
    {
        if(!$id){
            return $this->redirectToRoute('app_read_products');
        }

        $db = $doctrine->getManager();
        $produit = $db->getRepository(Produit::class)->find($id);

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // traitement de l'image envoyée
            $photo = $form->get('photo')->getData();
            // dd($photo);
            if($photo) {
                $originalFileName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' .$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('product_directory'), $newFileName
                    );
                } catch (FileException $e){
                    die();
                }
                
                $oldFileName = $produit->getPhoto();

                $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/' . $this->getParameter('product_directory') . $oldFileName;

                if($oldFileName !== $this->getParameter('generic_product_img')){
                    if(file_exists($oldFilePath)){
                        unlink($oldFilePath);
                    }
                }

                $produit->setPhoto($newFileName);

            } else {
                if($produit->getPhoto() === '' || $produit->getPhoto() === null){
                    $produit->setPhoto($this->getParameter('generic_product_img'));
                }
            }

            $db = $doctrine->getManager();
            $db->persist($produit);
            $db->flush();

            $this->addFlash('success', 'Le produit a été modifié avec succès.');
            return $this->redirectToRoute('app_read_products');
            
        }

        return $this->render('admin/update-product.html.twig', [
            'form' => $form->createView(),
            'imgName' => $produit->getPhoto(),
        ]);
    }

    public function deleteProduct(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        if(!$id){
            return $this->redirectToRoute('app_read_products');
        }

        $db = $doctrine->getManager();
        $produit = $db->getRepository(Produit::class)->find($id);
        $db->remove($produit);
        $db->flush();

        $this->addFlash('success', 'Le produit a été correctement supprimé.');
        return $this->redirectToRoute('app_read_products');
    }

    public function readUsers(): Response
    {
        return $this->render('admin/read-users.html.twig', [

        ]);
    }

    public function addUser(): Response|RedirectResponse
    {
        return $this->render('admin/add-user.html.twig', [

        ]);
    }

    public function updateUser(int $id): Response|RedirectResponse
    {
        return $this->render('admin/update-user.html.twig', [

        ]);
    }

    public function deleteUser(int $id): RedirectResponse
    {

        return $this->redirectToRoute('app_read_users');
    }
}