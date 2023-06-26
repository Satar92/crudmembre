<?php



namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

// ...


class ProfileController extends AbstractController
{
    // #[Route('/profile', name: 'app_profile')]
    public function index(AppHelpers $app): Response
    {

        $user = $app->getUser()->user;
        $profile = $user->getProfile();
        //dd($profile->getPseudo());


        return $this->render('profile/index.html.twig', [

            'profile' => $profile
        ]);
    }

    public function createProfile(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, AppHelpers $app): Response|RedirectResponse
    {
        $profile = new Profile();
        $user = $app->getUser()->user;
        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // traitement de l'image envoyée
            $photo = $form->get('photo')->getData();
            // dd($photo);
            if ($photo) {
                $originalFileName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('profile_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    die();
                }

                $profile->setPhoto($newFileName);
            } else {
                $profile->setPhoto($this->getParameter('generic_profile_img'));
            }


            $today= new \DateTime;
            $profile->setUpdatedAt($today);
            $profile->setCreatedAt($today);
            $profile->setStatut(1);
            $db = $doctrine->getManager();
            $db->persist($profile);
            $user->setProfile($profile);
            $db->persist($user);
            $db->flush();

            $this->addFlash('success', 'Le profile a été ajouté avec succès.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('/profile/create-profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function updateProfile(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger,AppHelpers $app,): Response|RedirectResponse
    {   

        $user = $app->getUser()->user;
        $profile = $user->getProfile();
        //dd($profile->getPseudo());
        if(!$profile){
            return $this->redirectToRoute('app_user_profile');
        }

        $db = $doctrine->getManager();
        $produit = $db->getRepository(Profile::class)->find($profile);

        $form = $this->createForm(ProfileType::class, $profile);

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
                        $this->getParameter('profile_directory'), $newFileName
                    );
                } catch (FileException $e){
                    die();
                }
                
                $oldFileName = $profile->getPhoto();

                $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/' . $this->getParameter('profile_directory') . $oldFileName;

                if($oldFileName !== $this->getParameter('generic_profile_img')){
                    if(file_exists($oldFilePath)){
                        unlink($oldFilePath);
                    }
                }

                $profile->setPhoto($newFileName);

            } else {
                if($profile->getPhoto() === '' || $profile->getPhoto() === null){
                    $profile->setPhoto($this->getParameter('generic_profile_img'));
                }
            }
            $today= new \DateTime;
            $profile->setUpdatedAt($today);
            $db = $doctrine->getManager();
            $db->persist($produit);
            $db->flush();

            $this->addFlash('success', 'Le profile a été modifié avec succès.');
            return $this->redirectToRoute('app_user_profile');
            
        }

        return $this->render('/profile/update-user.html.twig', [
            'form' => $form->createView(),
            'imgName' => $produit->getPhoto(),
        ]);
    }
}
