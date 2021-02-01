<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Photo;
use Symfony\Component\Routing\Annotation\Route;

class Observed extends AbstractController{
    /**
     * @Route ("/observed", name="observed")
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        $em = $this -> getDoctrine()->getManager();
        $latestPhotoPublic = $em-> getRepository( Photo::class)->findBy(['is_public' => true]);

        return $this->render('observed/index.html.twig', [
            'latestPhotoPublic' => $latestPhotoPublic
        ]);

    }
}