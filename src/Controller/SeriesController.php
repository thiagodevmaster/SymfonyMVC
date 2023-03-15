<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
        RedirectResponse,
        Request,
        Response,
    };
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{

    public function __construct(
        private SeriesRepository $seriesRepository, 
        private EntityManagerInterface $entityMaganer)
    {

    }


    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [
            'controller_name' => 'SeriesController',
            'seriesList' => $seriesList, 
        ]);
    }

    #[Route('/series/create', name: 'app_series_form', methods: ['GET'])]
    public function addSerieForm(): Response
    {
        return $this->render('series/form.html.twig');
    }

    #[Route('/series/create', name: 'app_series_addSeries', methods: ["POST"])]
    public function addSeries(Request $request): Response
    {
        $serieName = $request->request->get('name');
        $serie = new Series($serieName);

        $this->seriesRepository->save($serie, true);

        $this->addFlash('success', "Série {$serie->getName()} adicionada com sucesso.");
        

        return new RedirectResponse('/series');
    }

    #[Route('/series/remove/{serie}', 
            name: 'app_series_removeSerie', 
            methods: ['DELETE'], 
            requirements: ['id'=>'[0-9]+'] 
        )]
    public function removeSerie(Series $serie, Request $request): Response 
    {
       $this->seriesRepository->remove($serie, true);

       $this->addFlash('danger', "Série {$serie->getName()} removida com sucesso.");

        return new RedirectResponse('/series');
    }

    #[Route("/series/update/{serie}", 
        name: 'app_series_updateForm',
        methods: ["GET"],
        requirements: ['id' => '[0-9]+'],
        )]
    public function updateSerieForm(Series $serie): Response
    {   
        return $this->render('series/form.html.twig', [
            'controller_name' => 'SeriesController',
            'serie' => $serie,
        ]);
    }

    #[Route("/series/update/{serie}", 
        name: 'app_series_update' ,
        methods: ["PATCH"])]
    public function updateSerie(Series $serie, Request $request): Response
    {
        $serie->setName($request->get('name'));
        $this->entityMaganer->flush(); 
        $this->addFlash('success', "Série {$serie->getName()} alterada com sucesso.");
        return new RedirectResponse('/series');
    }
}
