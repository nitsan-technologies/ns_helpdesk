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
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use NITSAN\NsHelpdesk\Domain\Repository\TicketsRepository;
use NITSAN\NsHelpdesk\Domain\Repository\HelpdeskRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use NITSAN\NsHelpdesk\Domain\Repository\BackendUserRepository;
use NITSAN\NsHelpdesk\Domain\Repository\FrontendUserRepository;
use NITSAN\NsHelpdesk\Domain\Repository\TicketStatusRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as translate;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * TicketsController
 */
class TicketsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
    public $config;

    /**
     * Storage page
     *
     * @var int
     */
    protected $pid = null;

    /**
     * Complete Configuration
     *
     * @var array
     */
    protected $allConfig = null;

    /**
     * HelpdeskRepository
     */
    protected $helpdeskRepository = null;

    /**
     * TicketsRepository
     */
    protected $ticketsRepository = null;

    /**
     * TicketStatusRepository
     */
    protected $ticketStatusRepository = null;

    /**
     * DefaultAssigneeRepository
     */
    protected $assigneeRepository = null;

    /**
     * FrontendUserRepository
     *
     */
    protected $frontendUserRepository;

    /**
     * BackendUserRepository
     */
    protected $backendUserRepository;

    /**
     * PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var array
     */
    protected $userDetails;

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        PersistenceManager $persistenceManager,
        FrontendUserRepository $frontendUserRepository,
        TicketsRepository $ticketsRepository,
        TicketStatusRepository $ticketStatusRepository,
        HelpdeskRepository $helpdeskRepository
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->frontendUserRepository = $frontendUserRepository;
        $this->ticketsRepository = $ticketsRepository;
        $this->ticketStatusRepository = $ticketStatusRepository;
        $this->helpdeskRepository = $helpdeskRepository;
    }


    /**
     * Initializes this object
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->contentObject = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        // $this->allConfig = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
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
        if (\TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $this->userDetails = $this->beUser = $GLOBALS['BE_USER']->user;
        } else {
            $this->userDetails = $this->feUser = $GLOBALS['TSFE']->fe_user->user;
        }
    }

    /**
     * action dashboard
     *
     */
    public function dashboardAction()
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
        $customerReview = $this->ticketsRepository->getCustomerReview();
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
     */
    public function listAction()
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
     * @param \NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets
     */
    public function showAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets)
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
     */
    public function newAction()
    {
        $newTickets = new \NITSAN\NsHelpdesk\Domain\Model\Tickets();
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
     * @param \NITSAN\NsHelpdesk\Domain\Model\Tickets $newTickets
     * @return void
     */
    public function createAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $newTickets)
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

    public function sendMailNotification($sendingDetails = null, $template = null)
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
    public function assignForAll()
    {
        $this->view->assignMultiple(
            [
                'storagePid' => $this->allConfig['persistence']['storagePid'],
                'data' => $this->contentObject->data
            ]
        );
    }

    public function getMailTemplateDetails()
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
     */
    protected function sendTemplateEmail(
        array $recipient,
        array $sender,
        $subject,
        $templateName,
        array $variables = []
    ) {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        /*For use of Localize value */
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths']['0']);

        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();

        $mail = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');

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
     * @param  \NITSAN\NsHelpdesk\Domain\Model\Tickets  $tickets
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function closeTicketAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets)
    {
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData, (array)$postData);
        $rating = (int)(isset($requestData['rating']) ? $requestData['rating'] : 0);
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
     * @param  \NITSAN\NsHelpdesk\Domain\Model\Tickets  $tickets
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function reopenTicketAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets)
    {
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData, (array)$postData);
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
        return $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setArguments(['tx_nshelpdesk_helpdesk[action]' => 'show', 'tx_nshelpdesk_helpdesk[controller]' => 'Tickets', 'tx_nshelpdesk_helpdesk[tickets]' => $ticket])->build();
    }

    /**
     * Generates the action menu
     */
    protected function initializeModuleTemplate(
        ServerRequestInterface $request
    ): ModuleTemplate {
        return $this->moduleTemplateFactory->create($request);
    }
}
