<?php
namespace  App\Controller;

use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class My_controller
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */

class My_controller extends AbstractController
{
    /**
     * @Route ("/my/photos", name="my_photos")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $myPhotos = $em->getRepository(Photo::class)->findBy(['user' =>$this->getUser()]);
        return $this->render('my/index.html.twig', [
            'myPhotos' => $myPhotos
        ]);
    }

    /**
     * @Route ("/my/photos/set_private/{id}", name="my_photos_set_as_private")
     * @param int $id
     * @return RedirectResponse
     */
    public function myPhotoSetAsPrivate(int $id): RedirectResponse
    {
        $em = $this ->getDoctrine()->getManager();
        $myPhoto = $em->getRepository( Photo::class)->find($id);

        if ($this ->getUser() == $myPhoto->getUser())
        {
            try{
                $myPhoto->setIsPublic( 0 );
                $em->persist($myPhoto);
                $em->flush();
                $this ->addFlash ( 'success',  'ustawiono jako prywatne');

            }catch(\Exception $e){
                $this->addFlash( 'error',  'wystąpił problem');

            }
        } else {
            $this->addFlash( 'error', 'Nie jesteś właścicielem zdjęcia');
        }
        return $this->redirectToRoute('latest_photo');
    }

    /**
     * @Route ("/my/photos/set_pulic/{id}", name="my_photos_set_as_public")
     * @param int $id
     * @return RedirectResponse
     */
    public function myPhotoSetAsPublic(int $id): RedirectResponse
    {
        $em = $this ->getDoctrine()->getManager();
        $myPhoto = $em->getRepository( Photo::class)->find($id);


        if ($this ->getUser() == $myPhoto->getUser())
        {
            try{
                $myPhoto->setIsPublic( 1 );
                $em->persist($myPhoto);
                $em->flush();
                $this ->addFlash ( 'success',  'ustawiono jako prywatne');

            }catch(\Exception $e){
                $this->addFlash( 'error',  'wystąpił problem');

            }
        } else {
            $this->addFlash( 'error', 'Nie jesteś właścicielem zdjęcia');
        }
        return $this->redirectToRoute('latest_photo');
    }

    /**
     * @Route("/my/photos/remove/{id}", name="my_photos_remove")
     * @param int $id
     * @return RedirectResponse
     */
    public function myPhotoRemove(int $id){
        $em = $this->getDoctrine()->getManager();
        $myPhoto =  $em->getRepository( Photo::class)-> find ($id);

        if ($this->getUser() == $myPhoto->getUser()){
            $fileManager = new Filesystem();
            $fileManager->remove('images/hosting/'.$myPhoto->getFilename());
            if ($fileManager->exist('images/hosting/'.$myPhoto->getFilename())){
                $this->addFlash('error', 'nie udalo sie usunąc');
            }else {
                $em->remove(myPhoto);
                $em->flush();
            }
        }
        return $this->redirectToRoute('latest_photos');
    }
}