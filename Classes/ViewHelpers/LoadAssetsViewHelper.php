<?php
namespace NITSAN\NsHelpdesk\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class LoadAssetsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    protected $extPath;
    protected $config =[];
    protected $constant;

    public function render()
    {

        // Collect the settings.
        $settings = $this->templateVariableContainer->get('settings');
        $cData = $this->templateVariableContainer->get('cData');
        // Create pageRender instance.
        $pageRender = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $this->loadResource($pageRender, $settings, $cData);
    }

    /**
     * Load static CSS for the Forms
     * @param $pageRender
     * @param $settings
     * @param $data
     */
    public function loadResource($pageRender, $settings, $data)
    {
        $css ='';

        $css .= '
        .nsbtn.btn-' . $settings['buttonstyle'] . ' {
            background-color:' . $settings['buttonbg'] . ';
            color: ' . $settings['buttoncolor'] . ';
            box-shadow: none;
            cursor: pointer;
            font-weight: ' . $settings['fontstyle'] . ';
            min-width: 105px;
            padding-top: 8px;
            width: auto !important;
            outline: medium none;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
            border:1px solid ' . $settings['buttonbg'] . '
        }
        .ns-helpdesk-popup .nshelpdesk-popup-btn , .ns-helpdesk-popup .nshelpdesk-popup-header {
             background-color: ' . $settings['popupSettings']['popupcolor'] . ';
        }
        .ns-helpdesk-popup .submit-block input[type="submit"] {
             background-color: ' . $settings['popupSettings']['popupcolor'] . ';
             border: solid 1px ' . $settings['popupSettings']['popupcolor'] . ';
        }
        ';
        $pageRender->addCssInlineBlock('globalSettingsCSS', $css);
    }
}
