<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace NITSAN\NsHelpdesk\Widgets;

use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;

/**
 * Concrete TYPO3 information widget
 *
 * This widget will give some general information about TYPO3 version and the version installed.
 *
 * There are no options available for this widget
 */
class NsHelpdeskWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        private  WidgetConfigurationInterface $configuration,
        protected  ?StandaloneView $view = null,
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->options = $options;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setLayoutRootPaths([GeneralUtility::getFileAbsFileName('EXT:ns_helpdesk/Resources/Private/Layouts/')]);
        $this->view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:ns_helpdesk/Resources/Private/Partials/')]);
        $this->view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:ns_helpdesk/Resources/Private/Templates/')]);
        $this->view->setTemplate('Widget/Helpdesk');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nshelpdesk_domain_model_tickets');
        //Total tickets
        $totalTickets = $queryBuilder
            ->count('uid')
            ->from('tx_nshelpdesk_domain_model_tickets')
            ->executeQuery()
            ->fetchOne();

        //Total New tickets
        $totalNewTickets = $queryBuilder
            ->count('uid')
            ->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_status', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne();

        //Total closed tickets
        $totalClosedTickets = $queryBuilder
            ->count('uid')
            ->from('tx_nshelpdesk_domain_model_tickets')
            ->where(
                $queryBuilder->expr()->eq('ticket_status', $queryBuilder->createNamedParameter(2, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne();

        // Array to hold the total count for each rating
        $totalRatings = [];

        // Loop through ratings 1 to 5
        for ($i = 1; $i <= 5; $i++) {

            // Count the tickets with the current rating
            $totalRatings[$i] = $queryBuilder
                ->count('uid')
                ->from('tx_nshelpdesk_domain_model_tickets')
                ->where(
                    $queryBuilder->expr()->eq('ticket_rating', $queryBuilder->createNamedParameter($i, \PDO::PARAM_INT))
                )
                ->executeQuery()
                ->fetchOne();
        }

        // Calculate the total average rating
        $totalAvg = 0;
        $totalTicketsWithRating = 0;
        foreach ($totalRatings as $rating => $count) {
            $totalAvg += $rating * $count;
            $totalTicketsWithRating += $count;
        }

        $totalRatingsCount = array_sum($totalRatings);

        $totalRatingsAverage = 0;
        if ($totalTicketsWithRating > 0) {
            $totalRatingsAverage = $totalAvg / $totalTicketsWithRating;
        }

        $this->view->assignMultiple([
            'totalTickets' => $totalTickets,
            'totalClosedTickets' => $totalClosedTickets,
            'totalNewTickets' => $totalNewTickets,
            'totalRatings' => (int)round($totalRatingsAverage),
            'totalRatingsCount' => $totalRatingsCount,
            'totalRatingsDistribution' => $totalRatings
        ]);

        return $this->view->render();
    }
    public function getCssFiles(): array
    {
        return ['EXT:ns_helpdesk/Resources/Public/Css/backend/ns-helpdesk.css'];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
