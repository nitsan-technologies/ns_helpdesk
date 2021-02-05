<?php
namespace NITSAN\NsHelpdesk\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PageLayoutView implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{
    public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        $extKey = 'ns_helpdesk';
        if ($row['CType'] == 'list' && $row['list_type'] == 'nshelpdesk_helpdesk') {
            $drawItem = false;
            $headerContent = '';
            // template
            $view = $this->getFluidTemplate($extKey, 'NsHelpdesk');
            if (!empty($row['pi_flexform'])) {
                /** @var FlexFormService $flexFormService */
                if (version_compare(TYPO3_branch, '9.0', '>')) {
                    $flexFormService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Service\FlexFormService::class);
                } else {
                    $flexFormService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\FlexFormService::class);
                }
            }

            // assign all to view
            $view->assignMultiple(['flexformData' => $flexFormService->convertFlexFormContentToArray($row['pi_flexform'])]);

            // return the preview
            $itemContent = $parentObject->linkEditContent($view->render(), $row);
        }
    }

    /**
     * @param string $extKey
     * @param string $templateName
     * @return string the fluid template
     */
    protected function getFluidTemplate($extKey, $templateName)
    {
        // prepare own template
        $fluidTemplateFile = GeneralUtility::getFileAbsFileName('EXT:' . $extKey . '/Resources/Private/Backend/' . $templateName . '.html');
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename($fluidTemplateFile);
        return $view;
    }
}
