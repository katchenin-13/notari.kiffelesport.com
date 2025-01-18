<?php

namespace App\Controller\Actes;

use App\Entity\DocumentClient;
use App\Entity\Dossier;
use App\Entity\DossierWorkflow;
use App\Entity\Enregistrement;
use App\Entity\Identification;
use App\Entity\InfoClassification;
use App\Entity\Obtention;
use App\Entity\PaiementFrais;
use App\Entity\Piece;
use App\Entity\PieceVendeur;
use App\Entity\Redaction;
use App\Entity\Remise;
use App\Entity\SuiviDossierWorkflow;
use App\Repository\DocumentSigneRepository;
use App\Repository\CourierArriveRepository;
use App\Repository\FichierRepository;
use App\Repository\IdentificationRepository;
use App\Repository\PieceRepository;

use App\Form\Dossier3Type;
use App\Repository\DossierRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Client;
use App\Entity\CommentaireEng;
use App\Entity\CommentaireIdentification;
use App\Entity\CommentaireObtention;
use App\Entity\CommentairePaiement;
use App\Entity\CommentairePiece;
use App\Entity\CommentaireRedaction;
use App\Entity\CommentaireSignature;
use App\Entity\Compte;
use App\Entity\DocumentSigne;
use App\Entity\DocumentSigneFichier;
use App\Entity\Employe;
use App\Entity\EnregistrementDocument;
use App\Entity\RemiseActe;
use App\Entity\TypeClient;
use App\Form\DossierType;
use App\Repository\ClientRepository;
use App\Repository\DocumentClientRepository;
use App\Repository\DocumentTypeActeRepository;
use App\Repository\DossierWorkflowRepository;
use App\Repository\TypeRepository;
use App\Repository\WorkflowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Egulias\EmailValidator\Parser\Comment;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Language;

#[Route('/ads/actes/dossier')]
class DossierController extends BaseController
{
    use FileTrait;
    const TAB_ID = 'smartwizard-3';
    const INDEX_ROOT_NAME = 'app_actes_dossier_index';


    #[Route('/{id}/fullname', name: 'app_get_fullname', methods: ['DELETE', 'GET'])]
    public function getFullNameClient(ClientRepository $clientRepository, TypeClient $typeClient)
    {
        $response = new Response();
        $tabClient = array();
        $clients = $clientRepository->findBy(['type_client' => $typeClient]);
        $i = 0;

        foreach ($clients as $e) {
            // transformer la réponse de la requete en tableau qui remplira le select pour ensembles


            $tabClient[$i]['id'] = $e->getId();
            $tabClient[$i]['nom'] = $e->getTypeClient()->getCode() == "P" ? $e->getNom() . ' ' . $e->getPrenom() : $e->getRaisonSocial();


            $i++;
        }

        //$dataWithoutDoublon =array_unique($tabClient, SORT_REGULAR);

        $dataService = json_encode($tabClient); // formater le résultat de la requête en json
        /*  dd($dataService); */
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($dataService);
        /* } */
        return $response;
    }

    #[Route('/{id}/print-cr', name: 'app_reunion_print_cr', methods: ['DELETE', 'GET'])]
    public function printAction(Request $request, Dossier $dossier, IdentificationRepository $identificationRepository)
    {

        $language = new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::FR_FR);
        $phpWord  = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->getSettings()->setThemeFontLang($language);

        $phpWord->setDefaultFontName('Arial Narrow');

        $section = $phpWord->addSection([
            'orientation' => 'portrait',
        ]);

        $phpWord->addParagraphStyle('pJustify', array('align' => 'both', 'spaceBefore' => 3, 'spaceAfter' => 3, 'spacing' => 3));

        /* Utils::wordHeader($phpWord, $this->getParameter('assets_dir'), $section, 'landscape', null, 'first');
        Utils::wordFooter($section, 'landscape');


        $textBox = $section->addTextBox([
            'alignment'   => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0
            //, 'positioning' => 'absolute'

            , 'width' => 700,
            'height'      => 1,
            'borderSize'  => 2,
            'borderColor' => '#cf2e2e',
            //'borderStyle' => 'double',
            //'wrap' => 'square'
        ]); */



        $fontSize   = 7;
        $bold       = ['bold' => true, 'size' => $fontSize];
        $center     = ['align' => 'center', 'spaceAfter' => 0];
        $numRapport = 10 + 1;
        $padSize    = str_pad($numRapport, 3, '0', STR_PAD_LEFT);

        /* $section->addTextBreak(1.2); */
        $section->addText('BORDEREAU des ACTES DEPOSES
        COCODY
        01 JANVIER 2023
        ', ['bold' => true, 'size' => 14, 'underline' => 'single'], ['align' => 'center']);

        $box = $section->addTextBox([
            'alignment'   => \PhpOffice\PhpWord\SimpleType\Jc::START,
            'width' => 450,
            'height'      => 50,
            'borderSize'  => 2,
            'borderColor' => 'black',
            //'borderStyle' => 'double',
            //'wrap' => 'square'
        ]);
        $box->addText('(1) N°  du compte                                                                                                  Bordereau……….……………                                                  
        ',);
        $box->addText('Enreg le……..……………..…                                                                                 Vol……..f..°……...n°…………',);


        /* $section->addTextBox([
            'alignment'   => \PhpOffice\PhpWord\SimpleType\Jc::END,  'width' => 100,
            'height'      => 100,
            'borderSize'  => 2,
            'borderColor' => 'black',
            //'borderStyle' => 'double',
            //'wrap' => 'square'
        ]); */
        /* $section->addTextBreak(1); */






        $noSpace         = ['spaceAfter' => 0];
        $styleTable      = ['borderSize' => 6, 'borderColor' => '000000', 'cellPadding' => 40, 'cellMargin' => 40];
        $cellRowSpan     = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowSpanC     = ['vMerge' => 'continue', 'valign' => 'center'];
        $cellRowContinue = ['vMerge' => 'continue'];
        $cellColSpan     = ['gridSpan' => 2, 'valign' => 'center'];
        $cellHCentered   = ['align' => 'center'];
        $cellVCentered   = ['valign' => 'center'];
        $cellHRight      = ['align' => 'right'];

        $cellHCenteredNoSpace = array_merge($cellHCentered, $noSpace);

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);

        $w1             = 3500;
        $w2             = 1500;
        $noBorderBottom = ['borderBottomSize' => 0, 'borderBottomColor' => 'FFFFFF'];
        $noBorderTop    = ['borderTopSize' => 0, 'borderTopColor' => 'FFFFFF'];
        $noBorderLeft   = ['borderLeftSize' => 0, 'borderLeftColor' => 'FFFFFF'];
        $noBorderRight  = ['borderRightSize' => 0, 'borderRightColor' => 'FFFFFF'];

        $cellVCenteredBg = array_merge($cellVCentered, ['bgColor' => 'white', 'color' => 'FFFFFF', 'valign' => 'center', 'spaceAfter' => 0]);

        //c5e0b3

        //$section->addPageBreak();


        /*   $section = $phpWord->addSection([
            'orientation' => 'landscape',
        ]); */

        /* $bgColor = '#cf2e2e'; */
        $bgColor = '#ffff';

        $table = $section->addTable('Colspan Rowspan');


        $w2 = 5000;
        $w3 = 3000;
        $w4 = 25;
        $w5 = 1500;


        $cellRowSpan2 = array('vMerge' => 'restart');
        $cellRowContinue2 = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2);
        $cellColSpan3 = array('gridSpan' => 3);
        $cellColSpan6 = array('gridSpan' => 6);

        $cellVCenteredBg['bgColor'] = $bgColor;
        $table->addRow(null, ['cantSplit' => true]);
        $cell    = $table->addCell($w4, $cellVCenteredBg);
        $textRun = $cell->addTextRun($cellHCenteredNoSpace);
        $textRun->addText('N° D’ORDRE', ['bold' => false, 'allCaps' => true, 'color' => 'black'], ['align' => 'center']);
        $cell    = $table->addCell($w3, $cellVCenteredBg);
        $textRun = $cell->addTextRun($cellHCenteredNoSpace);
        $textRun->addText('DATE DE L’ACTE
et indication du nombre des rôles, mots et chiffres nuls
', ['bold' => false, 'allCaps' => true, 'color' => 'black'], ['align' => 'center']);
        $cell    = $table->addCell($w2, $cellColSpan2);
        $textRun = $cell->addTextRun($cellHCenteredNoSpace);
        $textRun->addText('NATURE    DE    L’ACTE
        Et noms des parties 
        ', ['bold' => false, 'allCaps' => true, 'color' => 'black'], ['align' => 'center']);
        $cell    = $table->addCell($w3, $cellVCenteredBg);
        $textRun = $cell->addTextRun($cellHCenteredNoSpace);
        $textRun->addText('MONTANT
        Des
        droits perçus
        ', ['bold' => false, 'allCaps' => true, 'color' => 'black'], ['align' => 'center']);
        $cell    = $table->addCell($w5, $cellVCenteredBg);
        $textRun = $cell->addTextRun($cellHCenteredNoSpace);
        $textRun->addText('NUMERO', ['bold' => false, 'allCaps' => true, 'color' => 'black'], ['align' => 'center']);

        /* NOUVELLE LIGNE */

        $table->addRow(null, ['cantSplit' => true, 'align' => 'center']);
        $table->addCell($w4, $cellRowSpan2)->addTextRun($cellHCenteredNoSpace)->addText("1", ['align' => 'center']);
        $table->addCell($w3, $cellRowSpan2)->addTextRun($cellHCenteredNoSpace)->addText("Date de l’acte : 
        31 décembre 2022 et Janvier 2023
        ………4 Rôles ½ 
        …00…/ …Renvois
        …00…/..Lignes nulles
        ……00/   Mots nuls
        …00…/…Chiffres
        ", ['align' => 'center']);
        $table->addCell($w2, $cellColSpan2)->addTextRun($cellHCenteredNoSpace)->addText("VENTE PAR MOSNIEUR " . $identificationRepository->findOneBy(array('dossier' => $dossier->getId()))->getAcheteur()->getNom() . ' ' . $identificationRepository->findOneBy(array('dossier' => $dossier->getId()))->getAcheteur()->getPrenom() . "AU PROFIT DE MONSIEUR" . $identificationRepository->findOneBy(array('dossier' => $dossier->getId()))->getVendeur()->getNom() . ' ' . $identificationRepository->findOneBy(array('dossier' => $dossier->getId()))->getVendeur()->getPrenom(), ['bold' => true, 'allCaps' => true, 'color' => 'black'],);
        $table->addCell($w3, $cellRowContinue2)->addText("");
        $table->addCell($w5, $cellRowSpan2)->addText("");

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell($w4, $cellRowContinue2);
        $table->addCell($w3, $cellRowContinue2);
        $table->addCell($w2, $cellColSpan2, ['borderSize' => 0, 'borderColor' => false])->addTextRun($cellHCenteredNoSpace)->addText("Lot 01 – Ilot 02                                                                     

        Titre Foncier 03 Cocody
        ", ['bold' => true, 'allCaps' => true, 'color' => 'black']);

        $table->addCell($w3, $cellRowContinue2)->addText("");
        $table->addCell($w5, $cellRowContinue2);

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell($w4, $cellRowContinue2);
        $table->addCell($w3, $cellRowContinue2);
        $table->addCell($w2, $cellColSpan2)->addTextRun($cellHCenteredNoSpace)->addText("REP: 1234                                                              DF", ['bold' => true, 'allCaps' => true, 'color' => 'black'],);
        $table->addCell($w3)->addTextRun($cellHCenteredNoSpace)->addText("18.000 FCFA", ['bold' => true, 'allCaps' => true, 'color' => 'black'],);
        $table->addCell($w5, $cellRowContinue2);

        /* Nouvelle ligne */
        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell($w4, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("2", ['align' => 'center']);
        $table->addCell($w3, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("Date de l’acte : 
        ………0 Rôle
        …00…/ …Renvois
        …00…/..Lignes nulles
        ……00/   Mots nuls
        …00…/…Chiffres
        ", ['align' => 'center']);
        $table->addCell($w2, $cellColSpan2)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w5, $cellVCenteredBg)->addText("");



        /* NOUVELLE LIGNE */

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell($w4, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("3");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w2, $cellColSpan2)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w5, $cellVCenteredBg)->addText("");

        /* NOUVELLE LIGNE */

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan3)->addTextRun($cellHCenteredNoSpace)->addText("Le   présent   bordereau   contenant    UN     acte 
         Numéroté de   1   à   1   est  certifié  exact  et  complet À     
         Abidjan, le  01 Janvier 2023.                                                                                          
         Signature du notaire                                
         ");
        $table->addCell(null, $cellColSpan3)->addTextRun($cellHCenteredNoSpace)->addText("Arrêt le présent bordereau à la somme  
        
        (en toutes lettres)                                                         
        Cachet du bureau :
        ");

        /* NOUVELLE LIGNE */

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan6)->addTextRun($cellHCenteredNoSpace)->addText("                             CADRE   RESERVE   A   L ’ADMINISTRATION   (Dépouillement des droits perçus)                               
         ", ['align' => 'center']);
        /*             NOUEVLLE LIGNE */

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan2)->addTextRun($cellHCenteredNoSpace)->addText("NATURE", $cellHCenteredNoSpace);
        $table->addCell($w3, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("NOMBRE");
        $table->addCell($w2, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("MONTANT");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addTextRun($cellHCenteredNoSpace)->addText("OBSERVATION");

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan2)->addText("", $cellHCenteredNoSpace);
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w2, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan2)->addText("", $cellHCenteredNoSpace);
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w2, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");

        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell(null, $cellColSpan2)->addText("", $cellHCenteredNoSpace);
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w2, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");
        $table->addCell($w3, $cellVCenteredBg)->addText("");




        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $fileName  = $this->getUploadDir('reunions/rapports', true) . '/rapport_reunion_' . $dossier->getNumeroOuverture() . '_' . date(strtotime("now")) . '.docx';
        $objWriter->save($fileName);

        return $this->file($fileName);
    }



    #[Route('/{etat}', name: 'app_actes_dossier_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $etat): Response
    {

        $clair = $request->query->get('clair');

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);



        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_actes_dossier_index', ['etat' => $etat, 'clair' => $clair])
        ])


            ->add('clair', EntityType::class, [
                'class' => Employe::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.fonction', 'f')
                        ->where('f.libelle = :libelle')
                        ->setParameter('libelle', 'CLERC')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => function ($employe) {
                    return $employe->getNom() . ' ' . $employe->getPrenom();
                },
                'label' => 'CLERC EN CHARGE',
                'required' => false,
                'attr' => ['class' => 'form-control has-select2'],
            ]);

        $table = $dataTableFactory->create()
            ->add('numeroOuverture', TextColumn::class, ['label' => 'N° ouverture'])
            // ->add('numeroRepertoire', TextColumn::class, ['label' => 'N° repertoire'])
            ->add('numcompte', TextColumn::class, ['label' => 'N° Compte'])
            ->add('dateCreation', DateTimeColumn::class,  ['label' => 'Date création', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('objet', TextColumn::class, ['label' => 'Objet'])
            ->add('employe', TextColumn::class, ['label' => 'Cler en charge', 'field' => 'emp.nom'])
            // ->add('typeActe', TextColumn::class, ['label' => 'Type d\'acte', 'field' => 't.titre'])
            ->add('natureDossier', TextColumn::class, ['label' => 'Type de dossier'])

            ->add('etape', TextColumn::class, ['className' => 'w-100px', 'field' => 'l.id', 'label' => 'Etape', 'render' => function ($value, Dossier $context) {


                return $context->getEtape() == '' ? 'Non entamé ' : $context->getEtape();
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Dossier::class,
                'query' => function (QueryBuilder $qb) use ($etat, $clair) {
                    $qb->select(['p', 't'])
                        ->from(Dossier::class, 'p')
                        ->join('p.entreprise', 'en')
                        ->leftjoin('p.employe', 'emp')
                        ->innerJoin('p.typeActe', 't')

                        ->orderBy('p.id ', 'DESC');




                    if ($etat == 'termine') {
                        $qb->andWhere("JSON_CONTAINS(p.etat, '1', '$.termine') = 1");
                    } elseif ($etat == 'archive') {
                        $qb->andWhere("JSON_CONTAINS(p.etat, '1', '$.archive') = 1");
                    } elseif ($etat == 'cree') {
                        $qb->andWhere("JSON_CONTAINS(p.etat, '1', '$.cree') = 1")
                        ->orWhere("JSON_CONTAINS(p.etat, '1', '$.en_cours') = 1");
                    }



                    if ($clair) {
                        $qb
                            ->andWhere('p.employe = :clair')
                            ->setParameter('clair', $clair);
                    }





                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                        ->setParameter('entreprise', $this->entreprise);
                    }
                }
            ])
            ->setName('dt_app_actes_dossier_' . $etat . '_' . $clair);

        $form = $builder->getForm();
        if ($permission != null) {

            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'suivi' => new ActionRender(function () use ($permission, $etat) {
                    if ($etat != 'termine') {
                        return true;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];

            $gridId = $etat . '_' . $clair;
            $hasActions = false;


            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions',
                    'orderable' => false,
                    'globalSearchable' => false,
                    'className' => 'grid_row_actions',
                    'render' => function ($value, Dossier $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_actes_dossier_edit', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-pen',
                                    'attrs' => ['class' => 'btn-default'],
                                    'render' => $renders['edit']
                                ],
                                'suivi' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('dossier_suivi', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-folder',
                                    'attrs' => ['class' => 'btn-dark'],
                                    'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_actes_dossier_show', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-eye',
                                    'attrs' => ['class' => 'btn-primary'],
                                    'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_actes_dossier_delete', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-trash',
                                    'attrs' => ['class' => 'btn-main'],
                                    'render' => $renders['delete']
                                ]
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('actes/dossier/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat,
            'grid_id' => $gridId,

            'form' => $form->createView(),
        ]);
    }


    #[Route('/dossier/{id}/suivi', name: 'dossier_suivi', methods: ['GET', 'POST'])]
    public function suivi(Request $request, Dossier $dossier, WorkflowRepository $workflowRepository)
    {
        $typeActe = $dossier->getTypeActe();
        $etapes = $workflowRepository->findBy(['active' => 1, 'typeActe' => $typeActe], ['numeroEtape' => 'asc']);
        //dd($etapes);
        return $this->render('actes/dossier/suivi.html.twig', [
            'dossier' => $dossier,
            'base_url' => $this->generateUrl('app_config_parametre_dossier_index'),
            'type_acte' => $typeActe,
            'etapes' => $etapes
        ]);
    }

    #[Route('/new/new', name: 'app_actes_dossier_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        DocumentTypeActeRepository $documentTypeActeRepository,
        WorkflowRepository $workflowRepository,
        EntityManagerInterface $em,
        DossierRepository $repository,
        TypeRepository $typeRepository
    ): Response {

        $dossier = new Dossier();
        $clientParties = new Identification();
        $dossier->addIdentification($clientParties);
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_actes_dossier_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            //recuperation du montant total des parties de ce dossiers
            $sommeMontantParties = 0;
            $parties = $form->get('identifications')->getData();
            foreach ($parties as $key => $value) {
                // Récupération et conversion du montant en nombre
                $montant = str_replace(' ', '', $value->getMontant());
                $montant = is_numeric($montant) ? (float)$montant : 0; // Validation et conversion

                // Ajout du montant à la somme
                $sommeMontantParties += $montant;
            }
            //formatage du montant total 
            $formatsommeMontantParties = str_replace(' ', '', $sommeMontantParties);


            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_dossier_index');

            $acteVente = $typeRepository->findOneBy(['code' => 'acte_vente']);
            $workflows = $workflowRepository->getFichier($acteVente->getId());
            $listeDocument = $documentTypeActeRepository->getListeDocument();

            // $redirect = $this->generateUrl('dossierActeVente');
            $date = (new \DateTime('now'))->format('Y-m-d');



            if ($form->isValid()) {

                //formatage de du montant total des parties
                $formatmontant = str_replace(' ', '', $dossier->getMontantTotal());

                if ($formatsommeMontantParties != $formatmontant) {
                    $statut = 0;
                    $message       = sprintf('Le montant total doit être égal à celui des honoraires');
                } else {
                    $currentDate = new \DateTime();

                    //creation de compte pour chaque partie
                    foreach ($parties as $key => $value) {
                        //verification du montant des parties
                        
                            //si ok alors on creer un  compte pour le client de pour ce dossier(compte pour la partie)
                            $compte = new Compte();
                            $compte->setClient($value->getClients())
                                ->setMontant($value->getMontant())
                                ->setSolde($value->getMontant())
                                //  ->setDossier($value->getDossier())
                                ->setActive(1);
                            $em->persist($compte);
                            $em->flush();
                        
                    }


                    foreach ($workflows as $workflow) {

                        $dossierWorkflow = new DossierWorkflow();
                        $nbre = $workflow->getNombreJours();
                        $dossierWorkflow->setDossier($dossier)
                            ->setWorkflow($workflow)
                            ->setDateDebut($currentDate);

                        $currentDate->modify("+{$nbre} day");
                        $dossierWorkflow->setDateFin($currentDate);

                        $dossier->addDossierWorkflow($dossierWorkflow);
                    }

                    $this->dossierWorkflow->getMarking($dossier);

                    $dossier->setEntreprise($this->entreprise);
                    $dossier->setTypeActe($acteVente);
                    $dossier->setEtape('');
                    $em->persist($dossier);
                    $entityManager->flush();

                    $data = true;
                    $message = 'Opération effectuée avec succès';
                    $statut = 1;
                    $this->addFlash('success', $message);
                }
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('actes/dossier/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
            'montant' => $dossier->getMontantTotal(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_actes_dossier_show', methods: ['GET'])]
    public function show(Dossier $dossier): Response
    {
        return $this->render('actes/dossier/show.html.twig', [
            'dossier' => $dossier,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_actes_dossier_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $entityManager,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        EntityManagerInterface $em,
        DossierRepository $repository,
        TypeRepository $typeRepository
    ): Response {

        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_actes_dossier_edit', [
                'id' => $dossier->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
         //recuperation du montant total des parties de ce dossiers
            $sommeMontantParties = 0;
            $parties = $form->get('identifications')->getData();
            foreach ($parties as $key => $value) {
                // Récupération et conversion du montant en nombre
                $montant = str_replace(' ', '', $value->getMontant());
                $montant = is_numeric($montant) ? (float)$montant : 0; // Validation et conversion

                // Ajout du montant à la somme
                $sommeMontantParties += $montant;
            }
            //formatage du montant total 
            $formatsommeMontantParties = str_replace(' ', '', $sommeMontantParties);

            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_dossier_index');


            if ($form->isValid()) {
                //formatage de du montant total des parties
                $formatmontant = str_replace(' ', '', $dossier->getMontantTotal());

                if ($formatsommeMontantParties != $formatmontant) {
                    $statut = 0;
                    $message       = sprintf('Le montant total doit être égal à celui des honoraires');
              } else {
                    $currentDate = new \DateTimeImmutable();
                    $currentDate->setTime(0, 0);
                    $acteVente = $dossier->getTypeActe();

                    $currentDate = new \DateTime();

                    //creation de compte pour chaque partie
                    foreach ($parties as $key => $value) {
                        //verification du montant des parties

                        //si ok alors on creer un  compte pour le client de pour ce dossier(compte pour la partie)
                        $compte = $em->getRepository(Compte::class)->findOneBy(['client' => $value->getClients()]);
                        if (!$compte) {
                            $compte = new Compte();
                        }
                        $compte->setClient($value->getClients())
                            ->setMontant($value->getMontant())
                            ->setSolde($value->getMontant())
                            // ->setDossier($value->getDossier())
                            ->setActive(1);
                        $em->persist($compte);
                        $em->flush();
                    }

                    $workflows = $workflowRepository->getFichier($acteVente->getId());
                    $dossierWorkflowRepository = $em->getRepository(DossierWorkflow::class);
                    foreach ($workflows as $workflow) {
                        $nbre = $workflow->getNombreJours();
                        if (!$dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $workflow])) {
                            $dossierWorkflow = new DossierWorkflow();
                            $dossierWorkflow->setDossier($dossier);

                            $dossierWorkflow->setDateDebut($currentDate);
                            $dateFin = $currentDate->modify("+{$nbre} day");
                        } else {
                            $dt = clone $dossierWorkflow->getDateDebut();
                            $dateFin = $dt->modify("+{$nbre} day");
                        }




                        $dossierWorkflow->setWorkflow($workflow)

                            ->setDateFin($dateFin);

                        $dossierWorkflow->setWorkflow($workflow)
                            ->setDateDebut($currentDate)
                            ->setDateFin($dateFin);

                        $dossier->addDossierWorkflow($dossierWorkflow);
                    }
                    $entityManager->persist($dossier);
                    $entityManager->flush();

                    $data = true;
                    $message = 'Opération effectuée avec succès';
                    $statut = 1;
                    $this->addFlash('success', $message);
              }
              
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('actes/dossier/edit.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_actes_dossier_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Dossier $dossier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_actes_dossier_delete',
                    [
                        'id' => $dossier->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($dossier);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_config_parametre_dossier_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut' => 1,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('actes/dossier/delete.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
        ]);
    }



    /**
     * @Route("/dossier/{id}/receuil-piece", name="acte_vente_piece", methods={"GET", "POST", "PUT"})
     *
     */
    public function piece(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository,
        FichierRepository $fichierRepository,
        DocumentClientRepository $documentClientRepository,
        IdentificationRepository $identificationRepository
    ) {


        $typeActe = $dossier->getTypeActe();
        //$documents =  $documentTypeActeRepository->getDocumentsEtape($typeActe, 'piece');

        /* dd($dossier->getPieces()); */
        $identification = $dossier->getIdentifications()->first();



        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);
        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        $oldPieces = $dossier->getPieces();

        //dd($dossier->getPieces());


        if (!$dossier->getCommentairePieces()->count()) {
            $commentaire = new CommentairePiece();
            $commentaire->setDescription("");
            $dossier->addCommentairePiece($commentaire);
        }

        if (!$dossier->getPieces()->count()) {

            /*   foreach ($dossier->getIdentifications() as $key => $value) {
                $piece = new Piece();
                $piece->setAttribut($value->getAttribut());
                $piece->setClient($value->getClients());
                $dossier->addPiece($piece);
            } */

            foreach ($dossier->getIdentifications() as $key => $value) {
                foreach ($documentClientRepository->findBy(['client' => $value->getClients()]) as $key => $doc) {
                    $piece = new Piece();
                    $piece->setAttribut($value->getAttribut());
                    $piece->setClient($value->getClients());
                    $piece->setPath($doc->getFichier());
                    $dossier->addPiece($piece);
                }
            }
        }

        //dd();


        /* dd($dossier->getPieces()); */



        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $filePath = 'acte_vente';
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'etape' => strtolower(__FUNCTION__),
            'current_etape' => $dossier->getEtape(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, $urlParams)
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {


            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();



            //dd($somme, str_replace(' ', '', $dossier->getMontantTotal()));


            if ($form->isValid()) {

                /*  if ($somme != str_replace(' ', '', $dossier->getMontantTotal())) {
                    $statut = 0;
                    $message       = sprintf('La somme total des montants %s doit être egal au montant honorais %s ', $somme, $dossier->getMontantTotal());
                } else { */
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));



                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }

                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];
                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }

                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                /*  }
 */
                $modal = false;
                $data = null;


                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView(),
            'montant' => $dossier->getMontantTotal(),
            'attributs' => $identificationRepository->getAttribut($dossier->getId()),
        ]);
    }




    // #[Route('/dossier/{id}/identification', name: 'acte_vente_identification', methods: ['GET', 'POST', 'PUT'])]    
    // public function identification(  Request $request,Dossier $dossier, EntityManagerInterface $em,FormError $formError,WorkflowRepository $workflowRepository, DossierWorkflowRepository $dossierWorkflowRepository) {
    //     $typeActe = $dossier->getTypeActe();
    //     $prefixe = $typeActe->getCode();
    //     $currentRoute = $request->attributes->get('_route');
    //     $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


    //     $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

    //     if(!$dossier->getCommentaireIdentifications()->count()){
    //         $commentaire = new CommentaireIdentification();
    //         $commentaire->setDescription("");
    //         $dossier->addCommentaireIdentification($commentaire);
    //     }

    //     if (!$dossier->getIdentifications()->count()) {
    //         $identification = new Identification();
    //         $dossier->addIdentification($identification);
    //     }

    //     $urlParams = ['id' => $dossier->getId()];


    //     $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());


    //     $form = $this->createForm(DossierType::class, $dossier, [
    //         'method' => 'POST',
    //         'current_etape' => $dossier->getEtape(),
    //         'etape' => strtolower(__FUNCTION__),
    //         'validation_groups' => ['Default', $routeWithoutPrefix],
    //         'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
    //     ]);
    //     $form->handleRequest($request);

    //     $data = null;
    //     $url = null;
    //     $tabId = null;
    //     $modal = true;

    //     $isAjax = $request->isXmlHttpRequest();



    //     if ($form->isSubmitted()) {

    //         $response = [];
    //         $redirect = $this->generateUrl($currentRoute, $urlParams);
    //         $isNext = $form->has('next') && $form->get('next')->isClicked();

    //         if ($form->isValid()) {
    //             if ($this->dossierWorkflow->can($dossier, 'post_creation')) {
    //                 $this->dossierWorkflow->apply($dossier, 'post_creation');
    //             }

    //             $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
    //             $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

    //             $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

    //             if (!$suivi) {
    //                 $date = new \DateTime();
    //                 $suivi = new SuiviDossierWorkflow();
    //                 $suivi->setDossierWorkflow($dossierWorkflow);
    //                 $suivi->setDateDebut($date);
    //                 $suivi->setDateFin($date);
    //             }
    //             if ($isNext && $next) {

    //                 $url = [
    //                     'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
    //                     'tab' => '#' . $next['route'],
    //                     'current' => '#' . $routeWithoutPrefix
    //                 ];
    //                 $hash = $next['route'];
    //                 $tabId = self::TAB_ID;
    //                 $redirect = $url['url'];

    //                 if (!$suivi->getEtat()) {
    //                     $suivi->setDateFin(new \DateTime());
    //                     $dossier->setEtape($next['route']);
    //                 }
    //                 $suivi->setEtat(true);
    //             } else {
    //                 $redirect = $this->generateUrl($currentRoute, $urlParams);
    //             }
    //             $modal = false;
    //             $em->persist($suivi);
    //             $em->persist($dossier);
    //             $em->flush();
    //             $data = null;

    //             $message       = 'Opération effectuée avec succès';
    //             $statut = 1;
    //             $this->addFlash('success', $message);
    //         } else {
    //             $message = $formError->all($form);
    //             $statut = 0;
    //             if (!$isAjax) {
    //                 $this->addFlash('warning', $message);
    //             }
    //         }


    //         if ($isAjax) {
    //             return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
    //         } else {
    //             if ($statut == 1) {
    //                 return $this->redirect($redirect);
    //             }
    //         }
    //     }


    //     return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
    //         'dossier' => $dossier,
    //         'route_without_prefix' => $routeWithoutPrefix,
    //         'form' => $form->createView()
    //     ]);
    // }

    /**
     * @Route("/dossier/{id}/redaction", name="acte_vente_redaction", methods={"GET", "POST"})
     *
     */
    public function redaction(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);


        if (!$dossier->getCommentaireRedactions()->count()) {
            $commentaire = new CommentaireRedaction();
            $commentaire->setDescription("");
            $dossier->addCommentaireRedaction($commentaire);
        }

        if (!$dossier->getRedactions()->count()) {
            $redaction = new Redaction();
            $redaction->setNumVersion(1);
            $dossier->addRedaction($redaction);
        }

        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());

        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'etape' => strtolower(__FUNCTION__),
            'current_etape' => $dossier->getEtape(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];


                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dossier/{id}/classification", name="acte_vente_classification", methods={"GET", "POST"})
     *
     */
    public function classification(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        if (!$dossier->getInfoClassification()) {
            $classification = new InfoClassification();
            $dossier->setInfoClassification($classification);
        }

        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());
        $validationGroups = ['Default', 'FileRequired', 'oui'];

        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'etape' => strtolower(__FUNCTION__),
            'current_etape' => current(array_keys($dossier->getEtat())),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();

        $data = null;

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isDone = $form->get('cloture')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                $redirect = $this->generateUrl($currentRoute, $urlParams);
                $modal = $isDone;

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }

                if ($isDone) {
                    if ($this->dossierWorkflow->can($dossier, 'cloture')) {
                        $this->dossierWorkflow->apply($dossier, 'cloture');
                    }
                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                    }
                    $suivi->setEtat(true);
                    $data = true;
                }
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();


                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dossier/{id}/signature-acte", name="acte_vente_signature", methods={"GET", "POST"})
     *
     */
    public function signature(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        if (!$dossier->getCommentaireSignatures()->count()) {
            $commentaire = new CommentaireSignature();
            $commentaire->setDescription("");
            $dossier->addCommentaireSignature($commentaire);
        }

        if (!$dossier->getDocumentSignes()->count()) {
            foreach ($dossier->getIdentifications() as $key => $value) {
                $documentSigne = new DocumentSigne();
                $documentSigne->setClient($value->getClients());

                $dossier->addDocumentSigne($documentSigne);
            }
        }
        if (!$dossier->getDocumentSigneFichiers()->count()) {

            $documentSigneFichier = new DocumentSigneFichier();
            $documentSigneFichier->setFichier(null);

            $dossier->addDocumentSigneFichier($documentSigneFichier);
        }


        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());
        $validationGroups = ['Default', 'FileRequired', 'oui'];

        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'current_etape' => $dossier->getEtape(),
            'etape' => strtolower(__FUNCTION__),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];

                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dossier/{id}/enregistrement-acte", name="acte_vente_enregistrement", methods={"GET", "POST"})
     *
     */
    public function enregistrement(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        $oldEnregistrements = $dossier->getEnregistrements();

        if (!$dossier->getCommentaireEngs()->count()) {
            $commentaire = new CommentaireEng();
            $commentaire->setDescription("");
            $dossier->addCommentaireEng($commentaire);
        }

        if (!$dossier->getEnregistrementDocuments()->count()) {
            $docs = new EnregistrementDocument();

            $dossier->addEnregistrementDocument($docs);
        }

        foreach (Enregistrement::SENS as $idSens => $value) {
            $hasValue = $oldEnregistrements->filter(function (Enregistrement $enregistrement) use ($idSens) {
                return $enregistrement->getSens() == $idSens;
            })->current();

            if (!$hasValue) {
                $enregistrement = new Enregistrement();
                $enregistrement->setSens(intval($idSens));
                $dossier->addEnregistrement($enregistrement);
            }
        }


        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());

        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'current_etape' => $dossier->getEtape(),
            'etape' => strtolower(__FUNCTION__),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];


                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }


    #[Route("/dossier/{id}/paiement-acte", name: "acte_vente_paiement", methods: ["GET", "POST"])]
    public function paiement(Request $request, Dossier $dossier, EntityManagerInterface $em, FormError $formError, WorkflowRepository $workflowRepository, DossierWorkflowRepository $dossierWorkflowRepository, DocumentClientRepository $documentClientRepository)
    {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        $oldEnregistrements = $dossier->getPaiementFrais();


        $ii = 1;


        if (!$dossier->getCommentairePaiements()->count()) {
            $commentaire = new CommentairePaiement();
            $commentaire->setDescription("");
            $dossier->addCommentairePaiement($commentaire);
        }

        if (!$dossier->getPaiementFrais()->count()) {

            foreach ($dossier->getIdentifications() as $key => $value) {

                $paiement = new PaiementFrais();
                $paiement->setAttribut($value->getAttribut());
                $paiement->setClient($value->getClients());
                $dossier->addPaiementFrai($paiement);
            }
        }



        /*   if (!$oldEnregistrements->count()) {

            foreach ($dossier->getPieces() as $key => $value) {
               
                $enregistrement = new PaiementFrais();
                $enregistrement->setClient($value->getClient());
                $dossier->addPaiementFrai($enregistrement);
            }
          
        } */
        /*  $enregistrement->setSens(intval($idSens)); */



        /*    foreach (PaiementFrais::Sens as $idSens => $value) {
            $hasValue = $oldEnregistrements->filter(function (PaiementFrais $enregistrement) use ($idSens) {
                return $enregistrement->getSens() == $idSens;
            })->current();



            if (!$hasValue) {
                $enregistrement = new PaiementFrais();
                $enregistrement->setSens(intval($idSens));
                $dossier->addPaiementFrai($enregistrement);
            }
        } */

        /*  dd($oldEnregistrements); */
        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());
        $validationGroups = ['Default', 'FileRequired', 'oui'];

        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'current_etape' => $dossier->getEtape(),
            'etape' => strtolower(__FUNCTION__),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {



            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            $dataLigne = $form->get('paiementFrais')->getData();



            /*   $resiltat = $dataLigne->filter(function (PaiementFrais $enregistrement) use ($dossier) {
                return $enregistrement->getSens() == 2;
            }); */

            //dd($resiltat[1]->getMontant(), $resiltat[1]->getDate(), $resiltat[1]->getPath());

            if ($form->isValid()) {


                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];


                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $message       = 'Opération effectuée avec succès';
                $statut = 1;


                $modal = false;
                $data = null;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView(),
            'montant' => $dossier->getMontantTotal(),
        ]);
    }

    /**
     * @Route("/dossier/{id}/titre-propriete", name="acte_vente_remise", methods={"GET", "POST"})
     *
     */
    public function remise(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        if (!$dossier->getRemises()->count()) {
            $remise = new Remise();
            $dossier->addRemise($remise);
        }

        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());
        $validationGroups = ['Default', 'FileRequired', 'oui'];

        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'current_etape' => $dossier->getEtape(),
            'etape' => strtolower(__FUNCTION__),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];

                    if (!$suivi->getEtat()) {
                        $suivi->setDateFin(new \DateTime());
                        $dossier->setEtape($next['route']);
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/dossier/{id}/obtention", name="acte_vente_obtention", methods={"GET", "POST"})
     *
     */
    public function obtention(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);
        if (!$dossier->getCommentaireObtentions()->count()) {
            $commentaire = new CommentaireObtention();
            $commentaire->setDescription("");
            $dossier->addCommentaireObtention($commentaire);
        }

        if (!$dossier->getObtentions()->count()) {
            $obtention = new Obtention();
            $dossier->addObtention($obtention);
        }



        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());

        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'etape' => strtolower(__FUNCTION__),
            'current_etape' => $dossier->getEtape(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];

                    if (!$suivi->getEtat()) {
                        $dossier->setEtape($next['route']);
                        $suivi->setDateFin(new \DateTime());
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/dossier/{id}/remise-acte", name="acte_vente_remise_acte", methods={"GET", "POST"})
     */
    public function remiseActe(
        Request $request,
        Dossier $dossier,
        EntityManagerInterface $em,
        FormError $formError,
        WorkflowRepository $workflowRepository,
        DossierWorkflowRepository $dossierWorkflowRepository
    ) {
        $typeActe = $dossier->getTypeActe();
        $prefixe = $typeActe->getCode();
        $currentRoute = $request->attributes->get('_route');
        $routeWithoutPrefix = str_replace("{$prefixe}_", '', $currentRoute);


        $current = $workflowRepository->findOneBy(['typeActe' => $typeActe, 'route' => $routeWithoutPrefix]);

        if (!$dossier->getRemiseActes()->count()) {
            $remise = new RemiseActe();
            $dossier->addRemiseActe($remise);
        }


        $urlParams = ['id' => $dossier->getId()];


        $next = $workflowRepository->getNext($typeActe->getId(), $current->getNumeroEtape());

        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $form = $this->createForm(DossierType::class, $dossier, [
            'method' => 'POST',
            'etape' => strtolower(snake_case(__FUNCTION__)),
            'current_etape' => $dossier->getEtape(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl($currentRoute, ['id' => $dossier->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $url = null;
        $tabId = null;
        $modal = true;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl($currentRoute, $urlParams);
            $isNext = $form->has('next') && $form->get('next')->isClicked();

            if ($form->isValid()) {

                $suiviDossierRepository = $em->getRepository(SuiviDossierWorkflow::class);
                $dossierWorkflow = $dossierWorkflowRepository->findOneBy(['dossier' => $dossier, 'workflow' => $current]);

                $suivi = $suiviDossierRepository->findOneBy(compact('dossierWorkflow'));

                if (!$suivi) {
                    $date = new \DateTime();
                    $suivi = new SuiviDossierWorkflow();
                    $suivi->setDossierWorkflow($dossierWorkflow);
                    $suivi->setDateDebut($date);
                    $suivi->setDateFin($date);
                }
                if ($isNext && $next) {

                    $url = [
                        'url' => $this->generateUrl($next['code'] . '_' . $next['route'], $urlParams),
                        'tab' => '#' . $next['route'],
                        'current' => '#' . $routeWithoutPrefix
                    ];
                    $hash = $next['route'];
                    $tabId = self::TAB_ID;
                    $redirect = $url['url'];

                    if (!$suivi->getEtat()) {
                        $dossier->setEtape($next['route']);
                        $suivi->setDateFin(new \DateTime());
                    }
                    $suivi->setEtat(true);
                } else {
                    $redirect = $this->generateUrl($currentRoute, $urlParams);
                }
                $modal = false;
                $em->persist($suivi);
                $em->persist($dossier);
                $em->flush();
                $data = null;

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId', 'modal'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render("actes/dossier/{$prefixe}/{$routeWithoutPrefix}.html.twig",  [
            'dossier' => $dossier,
            'route_without_prefix' => $routeWithoutPrefix,
            'form' => $form->createView()
        ]);
    }


    /**
     * @throws MpdfException
     */

    // #[Route('/imprime/etat/dossier', name: 'app_actes_dossier_imprime', methods: ['GET', 'POST'], options: ['expose' => true], condition: "request.query.has('filters')")]
    #[Route('/imprime/all/{etat}/{clair}', name: 'app_actes_dossier_imprime_all', methods: ['GET'])]
    public function imprimerEtatDossier(
        $etat = null,
        $clair = null,
        DossierRepository $dossierRepository
    ): Response {

        $employe = $dossierRepository->getEmployeNomPrenom($clair);
        $employes = $dossierRepository->findAll();
    dd('dssdsdsdsd');

        return $this->renderPdf('actes/dossier/imprime.html.twig', [
            // 'datas' => $dossierRepository->findBy($clair),
            'emploi' => $employe,
            'date' => new \DateTime(),
            'entreprise' => ' ',
            'employe' => $employe
        ], [
            'orientation' => 'P',
            'protected' => true,
            'format' => 'A4',
            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' => '',
        ]);
    }
}
