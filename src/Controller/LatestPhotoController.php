<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Photo;
use Symfony\Component\Routing\Annotation\Route;

class LatestPhotoController extends AbstractController{
    /**
     * @Route ("/latest", name="latest_photos")
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        $em = $this -> getDoctrine()->getManager();
        $latestPhotoPublic = $em-> getRepository( Photo::class)->findBy(['is_public' => true]);

        return $this->render('latest_photos/index.html.twig', [
            'latestPhotoPublic' => $latestPhotoPublic
        ]);

    }
}
