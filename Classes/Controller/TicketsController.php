<?php

namespace NITSAN\NsHelpdesk\Controller;

/***
 *
 * This file is part of the "NS Helpdesk" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020
 *
 ***/

use TYPO3\CMS\Core\Mail\MailMessage;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use NITSAN\NsHelpdesk\Domain\Model\Tickets;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use NITSAN\NsHelpdesk\Domain\Model\TicketStatus;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use NITSAN\NsHelpdesk\Domain\Repository\TicketsRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use NITSAN\NsHelpdesk\Domain\Repository\BackendUserRepository;
use NITSAN\NsHelpdesk\Domain\Repository\FrontendUserRepository;
use NITSAN\NsHelpdesk\Domain\Repository\TicketStatusRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as translate;
use NITSAN\NsHelpdesk\Domain\Repository\DefaultAssigneeRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

/**
 * TicketsController
 */
class TicketsController extends ActionController
{
    protected $templateService;
    protected $constantObj;
    protected $sidebarData;
    protected $dashboardSupportData;
    protected $generalFooterData;
    protected $premiumExtensionData;
    protected $constants;
    protected $contentObject = null;
    protected $beUser = null;
    protected $feUser = null;
    protected $isBackendUser = null;

    /**
     * TypoScript
     *
     * @var array
     */
    public array $config;

    /**
     * Storage page
     *
     * @var int
     */
    protected int|null $pid = null;

    /**
     * Complete Configuration
     *
     * @var array
     */
    protected array|null $allConfig = null;

    /**
     * TicketsRepository
     *
     * @var TicketsRepository
     */
    protected TicketsRepository $ticketsRepository;

    /**
     * TicketStatusRepository
     *
     * @var TicketStatusRepository
     */
    protected TicketStatusRepository $ticketStatusRepository;

    /**
     * DefaultAssigneeRepository
     *
     * @var DefaultAssigneeRepository
     */
    protected DefaultAssigneeRepository $assigneeRepository;

    /**
     * FrontendUserRepository
     *
     * @var FrontendUserRepository
     */
    protected FrontendUserRepository $frontendUserRepository;

    /**
     * BackendUserRepository
     *
     * @var BackendUserRepository
     */
    protected BackendUserRepository $backendUserRepository;

    /**
     * PersistenceManager
     *
     * @var PersistenceManager
     */
    protected PersistenceManager $persistenceManager;

    /**
     * @var array
     */
    protected array|null $userDetails;

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        PersistenceManager $persistenceManager,
        FrontendUserRepository $frontendUserRepository,
        TicketsRepository $ticketsRepository,
        TicketStatusRepository $ticketStatusRepository,
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->frontendUserRepository = $frontendUserRepository;
        $this->ticketsRepository = $ticketsRepository;
        $this->ticketStatusRepository = $ticketStatusRepository;
    }


    /**
     * Initializes this object
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->config = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->config = $this->config['plugin.']['tx_nshelpdesk_helpdesk.']['settings.'];
        $this->backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
    }

    /**
     * Initialize Action
     *
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();

        $this->ticketStatusRepository->getFromAll();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'] = isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk']) ? $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'] : '';
        $extConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'];
        if($extConfig) {
            $this->pid = $extConfig['globalStorage'];
        }

        //Set USER ..
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $this->userDetails = $this->beUser = $GLOBALS['BE_USER']->user;
        } else {
            $this->userDetails = $this->feUser = $GLOBALS['TSFE']->fe_user->user;
        }
    }

    /**
     * action dashboard
     *
     * @return ResponseInterface
     */
    public function dashboardAction(): ResponseInterface
    {
        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            $view = $this->initializeModuleTemplate($this->request);
        } else {
            $view = $this->view;
        }
        $totalTickets = $this->ticketsRepository->countAll();
        $assignToMe = $this->ticketsRepository->findByAssigneeId($this->beUser['uid'])->count();
        $newTicket = $this->ticketsRepository->findByTicketStatus(1)->count();
        $closeTicket = $this->ticketsRepository->findByTicketStatus(2)->count();
        $customerReviewDetails  = $this->ticketsRepository->getCustomerReview();
        $customerReview = $this->getCustomerReviewRatings($customerReviewDetails);
        $bootstrapVariable = 'data-bs';
        $isBackend = ApplicationType::fromRequest($this->request)->isBackend();
        $assign = [
            'action' => 'dashboard',
            'pid' => $this->pid,
            'rightSide' => $this->sidebarData,
            'dashboardSupport' => $this->dashboardSupportData,
            'totalTicket' => $totalTickets,
            'assignToMe' => $assignToMe,
            'newTicket' => $newTicket,
            'closeTicket' => $closeTicket,
            'isBackendUser' => $this->isBackendUser,
            'customerReview' => $customerReview,
            'userDetail' => $this->beUser,
            'bootstrapVariable' => $bootstrapVariable,
            'isBackend' => $isBackend

        ];
        $view->assignMultiple($assign);

        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            return $view->renderResponse();
        } else {
            return $this->htmlResponse();
        }
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            $view = $this->initializeModuleTemplate($this->request);
        } else {
            $view = $this->view;
        }
        $filterData = [];
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData, (array)$postData);
        //Disabled Query settings and storage page..
        $req = isset($requestData['tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1']) ? $requestData['tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1'] : [];
        $req['ticketStatus'] = isset($req['ticketStatus']) ? $req['ticketStatus'] : '';
        $req['ticketTypes'] = isset($req['ticketTypes']) ? $req['ticketTypes'] : '';
        $req['sword'] = isset($req['sword']) ? $req['sword'] : '';

        $statusChecked = $req['ticketStatus'];
        $typeChecked = $req['ticketTypes'];
        $sword = $req['sword'];
        $settings = $this->settings;
        $isBackend = ApplicationType::fromRequest($this->request)->isBackend();
        //Search criteria...
        if ($statusChecked) {
            $filterData['ticket_status'] = $statusChecked; // Search by Ticket Status like close, new, etc..
        }
        if ($sword) {
            $filterData['sword'] = $sword; // Search using word
        }

        if ($this->beUser) {
            //Check isAdmin user or not... True portion is for the Admin User and False is
            if ($this->beUser['admin']==1) {
                $tickets = $this->ticketsRepository->fetchTickets($filterData);
            } else {
                $filterData['userid'] =  $this->beUser['uid'];
                $filterData['backendUser'] = 1;
                $tickets = $this->ticketsRepository->fetchTickets($filterData);
            }
            // Assign Modal Classes for the code optimisation..
            $modal_classes = [
                'singleModalClass' => 'nsDeleteTicket',
                'multipleModalClass' => 'nsDeleteSelectedTicket',
                'singleDeletefor' => translate::translate('confrimSingleTicket', 'ns_helpdesk'),
                'multipleDeletefor' => translate::translate('confrimMultipleTicket', 'ns_helpdesk'),
            ];

            $assign = [
                'action' => 'list',
                'pid' => $this->pid,
                'rightSide' => $this->sidebarData,
                'dashboardSupport' => $this->dashboardSupportData,
                'modalClasses' => $modal_classes,
                'backend' => 1,
                'isShow' => 1,
                'userDetails' => $this->beUser
            ];
        } else {
            // For the Front End User
            if ($this->feUser) {
                $assign['isShow'] = 1;
                if (isset($this->feUser['uid'])) {
                    $filterData['userid'] =  $this->feUser['uid'];
                }
                $tickets = $this->ticketsRepository->fetchTickets($filterData);
                $assign['userDetails'] = $this->feUser;
            }
        }
        $bootstrapVariable = 'data-bs';
        $statusList = $this->ticketStatusRepository->findAll();
        $tickets = isset($tickets) ? $tickets : '';
        $assign['tickets'] = $tickets;
        $assign['statusList'] = $statusList;
        $assign['statusChecked'] = $statusChecked;
        $assign['bootstrapVariable'] = $bootstrapVariable;
        $assign['isBackend'] = $isBackend;
        $view->assignMultiple($assign);

        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            return $view->renderResponse();
        } else {
            return $this->htmlResponse();
        }
    }

    /**
     * action show
     *
     * @param Tickets $tickets
     * @return ResponseInterface
     */
    public function showAction(Tickets $tickets): ResponseInterface
    {
        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            $view = $this->initializeModuleTemplate($this->request);
        } else {
            $view = $this->view;
        }
        $assign = [
            'action' => 'list',
            'tickets' => $tickets,
            'userDetails' => $this->userDetails,
        ];
        if ($this->beUser) {
            $assign['backend'] = 1;
        }
        $isBackend = ApplicationType::fromRequest($this->request)->isBackend();
        $bootstrapVariable = 'data-bs';
        $assign['bootstrapVariable'] = $bootstrapVariable;
        $assign['isBackend'] = $isBackend;
        $view->assignMultiple($assign);

        if (ApplicationType::fromRequest($this->request)->isBackend()) {
            return $view->renderResponse();
        } else {
            return $this->htmlResponse();
        }
    }

    /**
     * action new
     *
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        $newTickets = GeneralUtility::makeInstance(Tickets::class);
        $assign = [
            'newTickets' => $newTickets,
            'userDetails' => $this->feUser
        ];
        $this->view->assignMultiple($assign);

        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param Tickets $newTickets
     * @return void
     */
    public function createAction(Tickets $newTickets): void
    {
        $settings = $this->settings;
        $backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);

        if ($settings['defaultAssigneeId']) {
            $assignee = $backendUserRepository->findByUid($settings['defaultAssigneeId']);
            $newTickets->setAssigneeId($assignee);
        }

        //Create slug from the subject first...
        $slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($newTickets->getTicketSubject())));
        //Check slug availability...
        $isSlugAvail = $this->ticketsRepository->getSlug($slug);
        $data = [];
        if (count($isSlugAvail) > 0) {
            foreach ($isSlugAvail as $row) {
                $data[] = $row->getSlug();
            }
            if (in_array($slug, $data)) {
                $count = 0;
                while (in_array(($slug . '-' . ++$count), $data));
                $slug = $slug . '-' . $count;
            }
        }
        // set Ticket Status
        if($newTickets->getTicketStatus() == null) {
            $ticketStatus =$this->ticketStatusRepository->findAll()[0];
            $newTickets->setTicketStatus($ticketStatus);
        }
        //set Ticket Slug
        $newTickets->setSlug($slug);
        //Set Ticket PostDate
        $newTickets->setTicketPostDate(new \DateTime());
        $this->ticketsRepository->add($newTickets);
        $this->persistenceManager->persistAll();
        $ticketCreator_User = $newTickets->getUserId();

        //get creator Email & Name...
        $creatorEmail = $ticketCreator_User->getEmail();
        $creatorName = $ticketCreator_User->getFirstName() . ' ' . $ticketCreator_User->getLastName();
        $strReplace = ['{visitor_name}', '{ticket_number}', '{ticket_assignee}'];
        $strWith = [$creatorName, $newTickets->getUid(), ($newTickets->getAssigneeId()->getRealName() ? $newTickets->getAssigneeId()->getRealName() : $newTickets->getAssigneeId()->getUsername())];

        $sendDetails = $this->getMailTemplateDetails();
        $sendDetails['settings']['body'] = str_replace($strReplace, $strWith, $this->settings['body']);
        $validEmail =  filter_var($creatorEmail, FILTER_VALIDATE_EMAIL);
        if ($validEmail) {
            $sendDetails['email_subject'] = $settings['visitorSubject'];
            $sendDetails['sender'] = [
                'email' => $settings['notify']['email']['adminMail'],
                'name' => $settings['notify']['email']['adminName']
            ];
            $sendDetails['receiver'] = [
                'email' => $creatorEmail,
                'name' => $creatorName
            ];
            $this->sendMailNotification($sendDetails, 'User/UserMail');
        }

        //send mail to the Admin if admin notify is enabled...
        if ($settings['notify']['adminNotify']) {
            $sendDetails['email_subject'] = $settings['adminSubject'];
            $sendDetails['receiver'] = [
                'email' => $settings['notify']['email']['adminMail'],
                'name' => $settings['notify']['email']['adminName']
            ];
            $sendDetails['sender'] = [
                'email' => $creatorEmail,
                'name' => $creatorName
            ];
            $sendDetails['tickets'] = $newTickets;
            $this->sendMailNotification($sendDetails, 'Admin/AdminNotify');
        }

        //send mail to the particular assignee...
        if ($settings['defaultAssigneeId'] && $newTickets->getAssigneeId()->getEmail()) {
            $sendDetails['email_subject'] = $settings['adminSubject'];
            $sendDetails['receiver'] = [
                'email' => $newTickets->getAssigneeId()->getEmail(),
                'name' => $newTickets->getAssigneeId()->getRealName()
            ];
            $sendDetails['sender'] = [
                'email' => $creatorEmail,
                'name' => $creatorName
            ];
            $sendDetails['tickets'] = $newTickets;
            $this->sendMailNotification($sendDetails, 'Admin/AdminNotify');
        }
        $response = [
            'code' => 200,
            'status' => 'success'
        ];
        echo json_encode($response);
        exit();
    }

    /**
     * sendMailNotification function
     *
     * @param array|null $sendingDetails
     * @param string|null $template
     * @return void
     */
    public function sendMailNotification(array|null $sendingDetails = null, string|null $template = null): void
    {
        $sender = $sendingDetails['sender'];
        $receiver = $sendingDetails['receiver'];
        $this->sendTemplateEmail([$receiver['email'] => $receiver['name']], [$sender['email'] => $sender['name']], $sendingDetails['email_subject'], $template, $sendingDetails);
    }

    /**
     * Assigns all values, which should be available in all views
     *
     * @return void
     */
    public function assignForAll(): void
    {
        $this->view->assignMultiple(
            [
                'storagePid' => $this->allConfig['persistence']['storagePid'],
                'data' => $this->contentObject->data
            ]
        );
    }

    /**
     * getMailTemplateDetails function
     *
     * @return array
     */
    public function getMailTemplateDetails(): array
    {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        return [
            'typo3' => [
                'sitename' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
                'systemConfiguration' => $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'],
            ],
            'normalizedParams' => $protocol . '://' . $_SERVER['HTTP_HOST'] . '/'
        ];
    }

    /**
     * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     * @return bool
     */
    protected function sendTemplateEmail(
        array $recipient,
        array $sender,
        $subject,
        $templateName,
        array $variables = []
    ): bool {
        /** @var StandaloneView $emailView */
        $emailView = GeneralUtility::makeInstance(StandaloneView::class);

        /*For use of Localize value */
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths']['0']);

        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();

        $mail = GeneralUtility::makeInstance(MailMessage::class);

        /*Mail*/
        $mail->setTo($recipient)->setFrom($sender)->setSubject($subject);
        // HTML Email
        $mail->html($emailBody);
        $variables['user']['attachment'] = isset($variables['user']['attachment']) ? $variables['user']['attachment'] : '';
        if ($variables['user']['attachment']) {
            if (count($variables['user']['attachment']) > 0) {
                foreach ($variables['user']['attachment'] as $at) {
                    $mail->attachFromPath($at);
                }
            }
        }
        $mail->send();
        $status = $mail->isSent();
        return $status;
    }

    /**
     * @param  Tickets  $tickets
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     *
     * @return void
     */
    public function closeTicketAction(Tickets $tickets): void
    {
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData, (array)$postData);
        $rating = (int)(isset($requestData['rating']) ? $requestData['rating'] : 0);
        /** @var TicketStatus $status */
        $status = $this->ticketStatusRepository->findByUid(2);
        $fromBackend = isset($requestData['fromBackend']) ? $requestData['fromBackend'] : '';
        $tickets->setTicketStatus($status);
        $tickets->setTicketRating($rating);

        $adminName = $this->settings['notify']['email']['adminName'];
        $adminEmail = $this->settings['notify']['email']['adminMail'];

        $creatorName = $tickets->getUserId()->getFirstName() . ' ' . $tickets->getUserId()->getLastName();
        $creatorEmail = $tickets->getUserId()->getEmail();

        $sendDetails = [];
        $sendDetails = $this->getMailTemplateDetails();
        $this->ticketsRepository->update($tickets);
        $this->persistenceManager->persistAll();
        $url = $this->getFrontendTicketUrl($tickets->getUid());

        $sendDetails['ticketUrl'] = $url;
        $sendDetails['tickets'] = $tickets;
        $sendDetails['tstatus'] = $status->getStatusTitle();
        if ($fromBackend) {
            $sendDetails['email_subject'] = translate::translate('nshelpdesk.close.ticket.mail.subject', 'NsHelpdesk');
            $sendDetails['sender'] = [
                'email' => $adminEmail,
                'name' => $adminName
            ];
            $sendDetails['receiver'] = [
                'email' => $creatorEmail,
                'name' => $creatorName
            ];

            $this->sendMailNotification($sendDetails, 'User/CloseTicket');
        } else {
            $sendDetails['email_subject'] = translate::translate('nshelpdesk.close.ticket.user.mail.subject', 'NsHelpdesk');
            $sendDetails['receiver'] = [
                'email' => $adminEmail,
                'name' => $adminName
            ];
            $sendDetails['sender'] = [

                'email' => $creatorEmail,
                'name' => $creatorName
            ];
            $this->sendMailNotification($sendDetails, 'Admin/CloseTicket');
        }
        echo json_encode(['status'=>'success', 'message'=>translate::translate('nshelpdesk.ticket.close.message', 'NsHelpdesk')]);
        exit();
    }

    /**
     * @param  Tickets  $tickets
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @return void
     */
    public function reopenTicketAction(Tickets $tickets): void
    {
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData, (array)$postData);
        /** @var TicketStatus $status */
        $status = $this->ticketStatusRepository->findByUid(3);
        $fromBackend = isset($requestData['tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1']['fromBackend']) ? $requestData['tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1']['fromBackend'] : '';
        $adminName = $this->settings['notify']['email']['adminName'];
        $adminEmail = $this->settings['notify']['email']['adminMail'];

        $creatorName = $tickets->getUserId()->getFirstName() . ' ' . $tickets->getUserId()->getLastName();
        $creatorEmail = $tickets->getUserId()->getEmail();

        $sendDetails = [];
        $sendDetails = $this->getMailTemplateDetails();

        $tickets->setTicketStatus($status);
        $this->ticketsRepository->update($tickets);
        $this->persistenceManager->persistAll();
        $sendDetails['ticketUrl'] = $this->getFrontendTicketUrl($tickets->getUid());
        $sendDetails['tickets'] = $tickets;
        $sendDetails['tstatus'] = $status->getStatusTitle();
        if ($fromBackend) {
            $sendDetails['email_subject'] = translate::translate('nshelpdesk.reopened.ticket.mail.subject', 'NsHelpdesk');
            $sendDetails['sender'] = [
                'email' => $adminEmail,
                'name' => $adminName
            ];
            $sendDetails['receiver'] = [

                'email' => $creatorEmail,
                'name' => $creatorName
            ];

            $this->sendMailNotification($sendDetails, 'User/CloseTicket');
        } else {
            $sendDetails['email_subject'] = translate::translate('nshelpdesk.reopened.ticket.user.mail.subject', 'NsHelpdesk');
            $sendDetails['receiver'] = [
                'email' => $adminEmail,
                'name' => $adminName
            ];
            $sendDetails['sender'] = [

                'email' => $creatorEmail,
                'name' => $creatorName
            ];
            $this->sendMailNotification($sendDetails, 'Admin/CloseTicket');
        }
        echo json_encode(['status'=>'success', 'message'=>translate::translate('nshelpdesk.ticket.reopen.message', 'NsHelpdesk')]);
        exit();
    }

    public function getFrontendTicketUrl($ticket)
    {
        return $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setArguments(['tx_nshelpdesk_helpdesklist[action]' => 'show', 'tx_nshelpdesk_helpdesklist[controller]' => 'Tickets', 'tx_nshelpdesk_helpdesklist[tickets]' => $ticket])->build();
    }

    /**
     * getCustomerReviewRatings function
     *
     * @param array $config
     * @return int
     */
    public function getCustomerReviewRatings(array $config): int
    {
        $totalAvg = ($config['total5Ratings'] * 5 + $config['total4Ratings'] * 4 + $config['total3Ratings'] * 3 + $config['total2Ratings'] * 2 + $config['total1Ratings'] * 1);

        $totalRatings = 0;
        if ($totalAvg > 0) {
            $totalRatings = $totalAvg / ($config['total5Ratings'] + $config['total4Ratings'] + $config['total3Ratings'] + $config['total2Ratings'] + $config['total1Ratings']);
        }
        return $totalRatings;
    }

    /**
     * initializeModuleTemplate
     *
     * @param ServerRequestInterface $request
     * @return ModuleTemplate
     */
    protected function initializeModuleTemplate(ServerRequestInterface $request): ModuleTemplate
    {
        return $this->moduleTemplateFactory->create($request);
    }
}
