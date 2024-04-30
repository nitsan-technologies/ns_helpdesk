<?php

namespace NITSAN\NsHelpdesk\Controller;

/***
 *
 * This file is part of the "Helpdesk" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020
 *
 ***/

use NITSAN\NsHelpdesk\NsTemplate\ExtendedTemplateService;
use NITSAN\NsHelpdesk\NsTemplate\TypoScriptTemplateConstantEditorModuleFunctionController;
use NITSAN\NsHelpdesk\NsTemplate\TypoScriptTemplateModuleController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\Inject as inject;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as translate;

/**
 * TicketsController
 */
class TicketsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    protected $templateService;
    protected $constantObj;
    protected $sidebarData;
    protected $dashboardSupportData;
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
     * Storage page ID
     *
     * @var int|null
     */
    protected $pid = null;

    /**
     * Complete Configuration
     *
     * @var array|null
     */
    protected $allConfig = null;

    /**
     * @var TypoScriptTemplateModuleController
     */
    protected $pObj;

    /**
     * helpdeskRepository
     *
     * @var \NITSAN\NsHelpdesk\Domain\Repository\HelpdeskRepository
     * @inject
     */
    protected $helpdeskRepository = null;

    /**
     * ticketsRepository
     *
     * @var \NITSAN\NsHelpdesk\Domain\Repository\TicketsRepository
     * @inject
     */
    protected $ticketsRepository = null;

    /**
     * ticketStatusRepository
     *
     * @var \NITSAN\NsHelpdesk\Domain\Repository\TicketStatusRepository
     * @inject
     */
    protected $ticketStatusRepository = null;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository;

    /**
     *
     */
    protected $backendUserRepository;
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var array
     *
     */
    protected $userDetails;

    /*
     * Inject helpdeskRepository
     *
     * @param \NITSAN\NsHelpdesk\Domain\Repository\HelpdeskRepository $helpdeskRepository
     * @return void
     */
    public function injectHelpdeskRepository(\NITSAN\NsHelpdesk\Domain\Repository\HelpdeskRepository $helpdeskRepository)
    {
        $this->helpdeskRepository = $helpdeskRepository;
    }

    /*
    * Inject ticketsRepository
    *
    * @param \NITSAN\NsHelpdesk\Domain\Repository\TicketsRepository $ticketsRepository
    * @return void
    */
    public function injectTicketsRepository(\NITSAN\NsHelpdesk\Domain\Repository\TicketsRepository $ticketsRepository)
    {
        $this->ticketsRepository = $ticketsRepository;
    }

    /*
    * Inject ticketStatusRepository
    *
    * @param \NITSAN\NsHelpdesk\Domain\Repository\TicketStatusRepository $ticketStatusRepository
    * @return void
    */
    public function injectTicketStatusRepository(\NITSAN\NsHelpdesk\Domain\Repository\TicketStatusRepository $ticketStatusRepository)
    {
        $this->ticketStatusRepository = $ticketStatusRepository;
    }

    /*
   * Inject frontendUserRepository
   *
   * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository
   * @return void
   */
    public function injectFrontendUserRepository(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /*
    * Inject persistenceManager
    *
    * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
    * @return void
    */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Initializes this object
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->contentObject = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $this->templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);
        $this->constantObj = GeneralUtility::makeInstance(TypoScriptTemplateConstantEditorModuleFunctionController::class);
        $this->allConfig = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->config = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->config = $this->config['plugin.']['tx_nshelpdesk_helpdesk.']['settings.'];
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->backendUserRepository = $objectManager->get(BackendUserRepository::class);
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

        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'] = isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk']) ? $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'] : '';
            $extConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ns_helpdesk'];
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ns_helpdesk'] = isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ns_helpdesk']) ? $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ns_helpdesk'] : '';
            $extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ns_helpdesk']);
        }
        if ($extConfig) {
            $this->pid = $extConfig['globalStorage'];
        }

        //Set USER ..
        if (TYPO3_MODE === 'BE') {
            $this->setConfiguration();
            $this->userDetails = $this->beUser = $GLOBALS['BE_USER']->user;
        } else {
            $this->userDetails = $this->feUser = $GLOBALS['TSFE']->fe_user->user;
        }
    }

    /**
     * action dashboard
     *
     * @return void
     */
    public function dashboardAction()
    {
        $totalTickets = $this->ticketsRepository->countAll();
        $assignToMe = $this->ticketsRepository->findByAssigneeId($this->beUser['uid'])->count();
        $newTicket = $this->ticketsRepository->findByTicketStatus(1)->count();
        $closeTicket = $this->ticketsRepository->findByTicketStatus(2)->count();
        $customerReview = $this->ticketsRepository->getCustomerReview();

        $this->view->assignMultiple([
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
            'bootstrapVariable' => 'data'

        ]);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {

        //Disabled Query settings and storage page..
        $req = GeneralUtility::_GP('tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1');
        $req['ticketStatus'] = isset($req['ticketStatus']) ? $req['ticketStatus'] : '';
        $req['ticketTypes'] = isset($req['ticketTypes']) ? $req['ticketTypes'] : '';

        $statusChecked = $req['ticketStatus'];
        // $typeChecked = $req['ticketTypes'];


        //Search criteria...
        if ($statusChecked) {
            $filterData['ticket_status'] = $statusChecked; // Search by Ticket Status like close, new, etc..
        }


        if ($this->beUser) {
            //Check isAdmin user or not... True portion is for the Admin User and False is
            if ($this->beUser['admin'] == 1) {
                $filterData = isset($filterData) ? $filterData : '';
                $tickets = $this->ticketsRepository->fetchTickets($filterData);
            } else {
                $filterData = isset($filterData) ? $filterData : '';
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

        $statusList = $this->ticketStatusRepository->findAll();
        $tickets = isset($tickets) ? $tickets : '';
        $assign['tickets'] = $tickets;
        $assign['statusList'] = $statusList;
        $assign['statusChecked'] = $statusChecked;
        $assign['bootstrapVariable'] = 'data';
        $this->view->assignMultiple($assign);
    }

    /**
     * action show
     *
     * @param \NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets
     * @return void
     */
    public function showAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets)
    {
        $assign = [
            'action' => 'list',
            'tickets' => $tickets,
            'userDetails' => $this->userDetails,
        ];
        $statusList = $this->ticketStatusRepository->findAll();
        $assign['statusList'] = $statusList;
        $assign['backend'] = $this->beUser ? 1 : 0;



        $assign['bootstrapVariable'] = 'data';
        $this->view->assignMultiple($assign);
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        $newTickets = new \NITSAN\NsHelpdesk\Domain\Model\Tickets();
        $assign = [
            'newTickets' => $newTickets,
            'userDetails' => $this->feUser
        ];
        $this->view->assignMultiple($assign);
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
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $backendUserRepository = $objectManager->get(BackendUserRepository::class);

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
        if ($newTickets->getTicketStatus() == null) {
            $ticketStatus = $this->ticketStatusRepository->findAll()[0];
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

    /*
     * Api configuration
     */
    private function setConfiguration()
    {
        //GET CONSTANTs
        $this->constantObj->init($this->pObj);
        $this->constants = $this->constantObj->main();
    }

    /**
     * action formsSettings
     *
     * @return void
     */
    public function getConstantsAction()
    {
        $menu = GeneralUtility::_GP('cat');
        $this->view->assignMultiple([
            'action' => $menu,
            'constant' => $this->constants,
            'bootstrapVariable' => 'data'
        ]);
    }

    /**
     * action saveConstant
     *
     * @return void
     */
    public function saveConstantAction()
    {
        //TYPO3 Defeault alert for the save constants..
        $this->saveMessage();
        return true;
    }

    public function saveMessage()
    {
        $alertTitle = translate::translate('helpdesk.alert.title', 'NsHelpdesk');
        $alertMsg = translate::translate('helpdesk.alert.msg', 'NsHelpdesk');
        $this->addFlashMessage(
            $alertMsg,
            $alertTitle,
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );
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
        $emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        /*For use of Localize value */
        $extensionName = $this->request->getControllerExtensionName();
        $emailView->getRequest()->setControllerExtensionName($extensionName);
        /*For use of Localize value */
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths']['0']);

        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();
        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $mail = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');

        /*Mail*/
        $mail->setTo($recipient)->setFrom($sender)->setSubject($subject);
        // HTML Email
        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $mail->html($emailBody);
        } else {
            $mail->setBody($emailBody, 'text/html');
        }
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
        $rating = (int)GeneralUtility::_GP('rating');
        $status = $this->ticketStatusRepository->findByUid(2);
        $fromBackend = isset(GeneralUtility::_GP('tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1')['fromBackend']) ? GeneralUtility::_GP('tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1')['fromBackend'] : '';
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
        echo json_encode(['status' => 'success', 'message' => translate::translate('nshelpdesk.ticket.close.message', 'NsHelpdesk')]);
        exit();
    }


    /**
     * @param  \NITSAN\NsHelpdesk\Domain\Model\Tickets  $tickets
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function reopenTicketAction(\NITSAN\NsHelpdesk\Domain\Model\Tickets $tickets)
    {
        $status = $this->ticketStatusRepository->findByUid(3);
        $fromBackend = isset(GeneralUtility::_GP('tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1')['fromBackend']) ? GeneralUtility::_GP('tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1')['fromBackend'] : '';
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
        echo json_encode(['status' => 'success', 'message' => translate::translate('nshelpdesk.ticket.reopen.message', 'NsHelpdesk')]);
        exit();
    }

    public function getFrontendTicketUrl($ticket)
    {
        return $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(true)
            ->setArguments(['tx_nshelpdesk_helpdesk[action]'
            => 'show', 'tx_nshelpdesk_helpdesk[controller]'
            => 'Tickets', 'tx_nshelpdesk_helpdesk[tickets]'
            => $ticket])->build();
    }
}
