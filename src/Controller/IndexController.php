<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\UploadPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\RequestAttributeValueSame;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(UploadPhotoType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form -> isValid()){
            $em = $this->getDoctrine()->getManager();

            if ($this -> getUser()) {
                /**@var UploadedFile $pictureFilename */
                 $pictureFilename = $form->get('filename')->getData();
                 if($pictureFilename){
                     try {
                         $originalFileName = pathinfo($pictureFilename->getClientOrginalName(),  PATHINFO_FILENAME);
                         $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFileName);
                         $newFileName = $safeFileName.'.'.$pictureFilename->guessExtension();
                         $pictureFilename->move('images/hosting'. $newFileName);

                         $entityPhotos = new Photo();
                         $entityPhotos -> setFilename($newFileName);
                         $entityPhotos  -> setIsPublic($form->get('is_public')->getData());
                         $entityPhotos->setUploadedAt(new \DateTime());
                         $entityPhotos->setUser($this->getUser());

                         $em->persist($entityPhotos);
                         $em -> flush();
                         $this->addFlash('success', 'Dodano zdjęcie!');

                     }catch (\Exception $e) {
                        $this->addFlash('error', 'wystąpił błąd');
                     }




                 }


            }
        }
        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
