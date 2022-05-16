<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    
    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/showData', name: 'showData')]
    public function showData(RequestStack $requestStack, Request $request, DataFromSensorRepository $dataFromSensorRepo, LocalRepository $localRepo): Response
    {

        //Création d'un object de filtrage
        $search = new DataSearch();
        $searchForm = $this->createForm(DataSearchType::class, $search);

        $searchForm->handleRequest($request);
       
        $session = $requestStack->getSession();

        // the second argument is the value returned when the attribute doesn't exist
        $session->set('type', $search->getType());
        $session->set('local', $search->getLocal());
        $session->set('frequence', $search->getFrequence());

        //Récuperation des données selon la page, le nombre de données limite et les filtres activés
        $datas = $dataFromSensorRepo->findDataBySearch($search);
        //Si aucunes données n'est enregistrées, la page d'erreur sera adaptée
        if ($datas == null){
            $local = $search->getLocal();
            if ($local == null){
                $local = "choisi";
            }
            return $this->redirectToRoute("noData", [
            'local' =>$local
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
            if ($dataForChart->getType()->getId() == 1) {
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
            } else if ($dataForChart->getType()->getId() == 2) {
                $humidityDataValue[] = $dataForChart->getValue();
                $humidityDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
            } else if ($dataForChart->getType()->getId() == 3) {
                $temperatureDataValue[] = $dataForChart->getValue();
                $temperatureDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");
            }
        }
        //Envoie des données vers la vue
        return $this->render('admin/showData.html.twig', [
            'datas' => $datas,
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
            if ($dataForChart->getType()->getId() == 1) {
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
            } else if ($dataForChart->getType()->getId() == 2) {
                $humidityDataValue[] = $dataForChart->getValue();
                $humidityDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");

            } else if ($dataForChart->getType()->getId() == 3) {
                $temperatureDataValue[] = $dataForChart->getValue();
                $temperatureDate[] = $dataForChart->getSendedAt()->format("d-m-y G:i");

            }
            
        }
        if ($lastData != null){
            //Ajout de deux heures à la date actuelle pour être compatible avec l'heure de la bdd (VPS)
            //$currentData = new DateTime("now + 2 hours");
            //Dev
            $currentData = new DateTime("now");
            //Récupartion de la date de la dernière donnée reçue
            $dateLastData = $lastData->getSendedAt();
            //Calcule de l'interval entre les deux dates
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
    public function downloadData(RequestStack $requestStack, Request $request, DataFromSensorRepository $dataFromSensorRepo)
    {
        //récuration de la date actuelle 
        $currentDate = new \DateTime("now");
        //Création du filtre qui va s'appliquer dans les données du PDF
        $search = new DataSearch();
        //Récupération des données de session 
        $session = $requestStack->getSession();
        //Mise en place du fltre selon les données récupérées lors de l'affichage globale des données
        if($session->get('type')){
            $search->setType($session->get('type'));
        }
        if ($session->get('local')){
            $search->setLocal($session->get('local'));
        }
        if ($session->get('frequence')){
            $search->setFrequence($session->get('frequence'));
        }
        
        //Recherche des données selon le filtre
        $datas = $dataFromSensorRepo->findDataBySearch($search);            
        //Création du PDF en y ajoutant différentes options
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

        $fichier = 'HELB_Rapport_Donnees_'.$currentDate->format("d-m-yG:i:ss").".pdf";

        $dompdf->stream($fichier, [
            'Attachement' => true
        ]);

        return new Response();
    }

    #[Route('/admin/settings', name: 'settings')]
    public function settings(Request $request, ManagerRegistry $manager, UserRepository $userRepo, LocalRepository $localRepo, DataTypeRepository $datatypeRepo): Response
    {   
        $user = $userRepo->findCurrentUser($this->getUser()->getUserIdentifier());
        $currentUserCampus = $user->getCampus();
        
        $dataTypeList = $datatypeRepo->findAll();
        $localList = $localRepo->findLocalByCampus($currentUserCampus);
        $userList = $userRepo->findAllByDesc();
        
        $local = new Local();
        
        $localForm = $this->createForm(AddLocalType::class, $local);

        $localForm->handleRequest($request);
        $local->setCampus($currentUserCampus);
        
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