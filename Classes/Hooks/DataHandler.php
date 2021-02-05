<?php
namespace NITSAN\NsHelpdesk\Hooks;

use NITSAN\NsHelpdesk\Backend\TicketSlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook into TCE-Main which is used to show preview of Helpdesk item
 *
 */
class DataHandler
{
    /**
     * Fill path_segment/slug field with title
     *
     * @param string $status
     * @param string $table
     * @param string|int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject)
    {
        if ($table === 'tx_nshelpdesk_domain_model_tickets' && $status === 'new' && version_compare(TYPO3_branch, '9.5', '<')) {
            if (!isset($fieldArray['slug']) || empty($fieldArray['slug'])) {
                $slugHelperFor8 = GeneralUtility::makeInstance(TicketSlugHelper::class);
                $fieldArray['slug'] = $slugHelperFor8->sanitize($fieldArray['ticket_subject']);
            }
        }
    }
}
