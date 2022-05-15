<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Local;
use App\Entity\DataType;
use App\Entity\DataSearch;
use App\Form\AddLocalType;
use App\Form\DataSearchType;
use App\Form\AddDataTypeType;
use App\Repository\UserRepository;
use App\Repository\LocalRepository;
use App\Repository\DataTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DataFromSensorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    
    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/showData', name: 'showData')]
    public function showData(Request $request, DataFromSensorRepository $dataFromSensorRepo, LocalRepository $localRepo): Response
    {
        //Création d'un object de filtrage
        $search = new DataSearch();
        $lastLocal = $localRepo->findLastLocalByCampus("HELB");
        $searchForm = $this->createForm(DataSearchType::class, $search);

        $searchForm->handleRequest($request);
        //Nombre total des données permettant de définir le nombre de page nécessaire pour la pagination
        $total = $dataFromSensorRepo->findPaginatedCount($search);
        //Limite d'éléments par page
        $limit = 100;
        //Page par défaut 
        $page = (int) $request->query->get("page", 1);
        //Récuperation des données selon la page, le nombre de données limite et les filtres activés
        $datas = $dataFromSensorRepo->findDataBySearchPaginated($search, $page, $limit);
        //Si aucunes données n'est enregistrées, la page d'erreur sera adaptée
        if ($datas == null){
            return $this->redirectToRoute("noData", [
            'local' => $search->getLocal()
        ]);
        }
        //Création des tableaux nécessaires aux graphiques
        $co2DataValue = [];
        $humidityDataValue = [];
        $temperatureDataValue = [];
        $co2Date = [];
        $humidityDate = [];
        $temperatureDate = [];
        //Compteur de données pour le graphique en donut
        $goodCo2Counter = 0;
        $midCo2Counter = 0;
        $badCo2Counter = 0;
        //Boucle sur les données récupérées
        foreach ($datas as $dataForChart) {
            //Attribution des données dans les différents tableaux
            if ($dataForChart->getType()->getValue() == "CO2") {
                $midValue = $dataForChart->getValue();
                $co2DataValue[] = $midValue;
                $co2Date[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
                if($midValue < 600){
                    $goodCo2Counter ++;
                }else if ($midValue >= 600 && $midValue < 900){
                    $midCo2Counter ++;
                }else{
                    $badCo2Counter++;
                }
            } else if ($dataForChart->getType()->getValue() == "Humidity") {
                $humidityDataValue[] = $dataForChart->getValue();
                $humidityDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
            } else if ($dataForChart->getType()->getValue() == "Temperature") {
                $temperatureDataValue[] = $dataForChart->getValue();
                $temperatureDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
            }
        }
        //Envoie des données vers la vue
        return $this->render('admin/showData.html.twig', [
            'datas' => $datas,
            'total' => $total,
            'limit' => $limit,
            'co2DataValue' => json_encode($co2DataValue),
            'humidityDataValue' => json_encode($humidityDataValue),
            'temperatureDataValue' => json_encode($temperatureDataValue),
            'dataValue' => json_encode($temperatureDataValue),
            'co2Date' => json_encode($co2Date),
            'humidityDate' => json_encode($humidityDate),
            'temperatureDate' => json_encode($temperatureDate),
            'goodCo2Counter' => json_encode($goodCo2Counter),
            'midCo2Counter' => json_encode($midCo2Counter),
            'badCo2Counter' => json_encode($badCo2Counter), 
            'searchForm' => $searchForm->createView(),
        ]);
    }

    #[Route('/admin/stage1', name: 'stage1')]
    public function stage1(): Response
    {
        return $this->render('admin/stage1.html.twig', []);
    }

    #[Route('/admin/stage2', name: 'stage2')]
    public function stage2(): Response
    {
        return $this->render('admin/stage2.html.twig', []);
    }

    #[Route('/admin/localDetails/{local}', name: 'localDetails')]
    public function localDetails(String $local, DataFromSensorRepository $dataFromSensorRepo, Request $request, LocalRepository $localRepo): Response
    {
        $locals = $localRepo->findLocalByCampus("HELB");
        $total = $dataFromSensorRepo->findCountOfDataByLocal($local);
        
        $limit = 100;

        $page = (int) $request->query->get("page", 1);
        //$dataFromDB = $dataFromSensorRepo->findByLocal($local, "DESC");
        $dataFromDB = $dataFromSensorRepo->findDataByLocalPaginatedDESC($page, $limit, $local);
        $lastData = $dataFromSensorRepo->findLastDataByLocal($local);
        //dd($total);
        if ($dataFromDB == null){
            return $this->redirectToRoute("noData", [
            'local' => $local
        ]);
        }
        
        $co2DataValue = [];
        $humidityDataValue = [];
        $temperatureDataValue = [];
        $co2Date = [];
        $humidityDate = [];
        $temperatureDate = [];

        $goodCo2Counter = 0;
        $midCo2Counter = 0;
        $badCo2Counter = 0;
        $interval = 0;

        foreach ($dataFromDB as $dataForChart) {
            if ($dataForChart->getType()->getValue() == "CO2") {
                $midValue = $dataForChart->getValue();
                $co2DataValue[] = $midValue;
                $co2Date[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
                
                if($midValue < 600){
                    $goodCo2Counter ++;
                }else if ($midValue >= 600 && $midValue < 900){
                    $midCo2Counter ++;
                }else{
                    $badCo2Counter++;
                }
            } else if ($dataForChart->getType()->getValue() == "Humidity") {
                $humidityDataValue[] = $dataForChart->getValue();
                $humidityDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");

            } else if ($dataForChart->getType()->getValue() == "Temperature") {
                $temperatureDataValue[] = $dataForChart->getValue();
                $temperatureDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");

            }
            
        }
        if ($lastData != null){
            $currentData = new \DateTime("now");
            $dateLastData = $lastData->getSendedAt();
            $interval = $currentData->diff($dateLastData);
        }
        
        
        return $this->render('admin/localDetails.html.twig', [
            'locals' => $locals,
            'datas' => $dataFromDB,
            'lastData' => $lastData,
            'interval' => $interval,
            'total' => $total,
            'limit' => $limit,
            'co2DataValue' => json_encode($co2DataValue),
            'humidityDataValue' => json_encode($humidityDataValue),
            'temperatureDataValue' => json_encode($temperatureDataValue),
            'dataValue' => json_encode($temperatureDataValue),
            'co2Date' => json_encode($co2Date),
            'humidityDate' => json_encode($humidityDate),
            'temperatureDate' => json_encode($temperatureDate),
            'goodCo2Counter' => json_encode($goodCo2Counter),
            'midCo2Counter' => json_encode($midCo2Counter),
            'badCo2Counter' => json_encode($badCo2Counter),

        ]);
    }

    #[Route('/admin/downloadData', name: 'downloadData')]
    public function downloadData(Request $request, DataFromSensorRepository $dataFromSensorRepo)
    {
        $currentDate = new \DateTime("now");

        $search = new DataSearch();

        $searchForm = $this->createForm(DataSearchType::class, $search);

        $searchForm->handleRequest($request);

        $search->setLocal($request->query->get("local"));

        dd($search);
        $datas = $dataFromSensorRepo->findDataBySearch($search);            
        
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);

        $html = $this->renderView('admin/downloadData.html.twig',[
            'datas' => $datas
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fichier = 'infos'.$currentDate->format("d-m-yG:i:ss").".pdf";

        $dompdf->stream($fichier, [
            'Attachement' => true
        ]);

        return new Response();
    }

    #[Route('/admin/settings', name: 'settings')]
    public function settings(Request $request, ManagerRegistry $manager, UserRepository $userRepo, LocalRepository $localRepo, DataTypeRepository $datatypeRepo): Response
    {
        $dataTypeList = $datatypeRepo->findAll();
        $localList = $localRepo->findLocalByCampus("HELB");
        $userList = $userRepo->findAllByDesc();
        
        $local = new Local();
        
        $localForm = $this->createForm(AddLocalType::class, $local);

        $localForm->handleRequest($request);
        if ($localForm->isSubmitted() && $localForm->isValid()) {
            
            $manager->getManager()->persist($local);
            $manager->getManager()->flush();

            $this->addFlash("success", "Le local à bien été créé");
            return $this->redirectToRoute('settings');
        }

        $dataType = new DataType();
        
        $dataTypeForm = $this->createForm(AddDataTypeType::class, $dataType);

        $dataTypeForm->handleRequest($request);
        if ($dataTypeForm->isSubmitted() && $dataTypeForm->isValid()) {
            
            $manager->getManager()->persist($dataType);
            $manager->getManager()->flush();

            $this->addFlash("success", "Le type à bien été créé");
            return $this->redirectToRoute('settings');
        }
        return $this->render('admin/settings.html.twig', [
            "addLocalForm" => $localForm->createView(),
            "addDataTypeForm" => $dataTypeForm->createView(),
            "userList" => $userList,
            "localList" => $localList,
            "dataTypeList" =>$dataTypeList
        ]);
    }
}