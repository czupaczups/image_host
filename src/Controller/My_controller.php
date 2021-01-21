<?php
namespace  App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class My_controller extends AbstractController
{
    /**
     * @Route ("/my/photos", name="my_photos")
     */
    public function index()
    {

    }

    /**
     * @Route ("/my/photos/set_private/{id}", name="my_photos_set_as_private")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotoSetAsPrivate(int $id): \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotoSetAsPublic(int $id): \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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