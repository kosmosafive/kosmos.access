<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

global $APPLICATION;

$bodyClass = $APPLICATION->GetPageProperty('BodyClass');
$APPLICATION->SetPageProperty('BodyClass', ($bodyClass ? $bodyClass . ' ' : '') . 'no-background no-all-paddings');

Loader::includeModule('ui');
Extension::load(['ui.buttons', 'ui.icons', 'ui.notification', 'kosmosaccess.accessrights']);

Loc::loadMessages(__FILE__);

$componentAccessGroupId = $arResult['CODE'] . '-access-group';
$componentConfigPermissionsId = $arResult['CODE'] . '-config-permissions';

$initPopupEvent = $arResult['CODE'] . ':onComponentLoad';
$openPopupEvent = $arResult['CODE'] . ':onComponentOpen';
Toolbar::deleteFavoriteStar();

$hasFatals = false;

foreach ($arResult['ERROR'] as $error) {
    if ($error['TYPE'] === 'FATAL') {
        ?>
        <div class="task-message-label error"><?= htmlspecialcharsbx($error['MESSAGE']) ?></div><?php
        $hasFatals = true;
    }
}
?>
    <span id="<?= $componentAccessGroupId ?>"></span>
<?php
$APPLICATION->IncludeComponent(
    "kosmos:main.ui.selector",
    ".default",
    [
        'API_VERSION' => 3,
        'ID' => $componentAccessGroupId,
        'BIND_ID' => $componentAccessGroupId,
        'ITEMS_SELECTED' => [],
        'CALLBACK' => [
            'select' => "AccessRights.onMemberSelect",
            'unSelect' => "AccessRights.onMemberUnselect",
            'openDialog' => 'function(){}',
            'closeDialog' => 'function(){}',
        ],
        'OPTIONS' => [
            'eventInit' => $initPopupEvent,
            'eventOpen' => $openPopupEvent,
            'useContainer' => 'Y',
            'lazyLoad' => 'Y',
            'context' => $arResult['CODE'],
            'contextCode' => '',
            'useSearch' => 'Y',
            'useClientDatabase' => 'N',
            'enableAll' => 'N',
            'enableUsers' => 'Y',
            'enableDepartments' => 'N',
            'enableGroups' => 'N',
            'departmentSelectDisable' => 'Y',
            'allowAddUser' => 'Y',
            'allowAddCrmContact' => 'N',
            'allowAddSocNetGroup' => 'N',
            'allowSearchNetworkUsers' => 'N',
            'useNewCallback' => 'Y',
            'multiple' => 'Y',
            'enableSonetgroups' => 'N',
            'showVacations' => 'Y',
        ]
    ],
    false,
    ["HIDE_ICONS" => "Y"]
);
?>
<?php
if (!$hasFatals): ?>
    <div id="<?= $componentConfigPermissionsId ?>"></div>
    <script>
        const adminWrapper = document.querySelector('.adm-workarea');
        if (adminWrapper) {
            adminWrapper.classList.remove('adm-workarea');
            adminWrapper.style.padding = '6px 20px 0 20px';
        }

        let AccessRights = new BX.KosmosAccess.AccessRights({
            component: '<?=$component->getName()?>',
            renderTo: document.getElementById('<?=$componentConfigPermissionsId?>'),
            userGroups: <?= CUtil::PhpToJSObject($arResult['USER_GROUPS']) ?>,
            accessRights: <?= CUtil::PhpToJSObject($arResult['ACCESS_RIGHTS']); ?>,
            initPopupEvent: '<?= $initPopupEvent ?>',
            openPopupEvent: '<?= $openPopupEvent ?>',
            popupContainer: '<?= $componentAccessGroupId ?>',
            signedParameters: '<?= $component->getSignedParameters() ?>'
        });

        AccessRights.draw();
        setTimeout(function () {
            BX.onCustomEvent('<?= $initPopupEvent ?>', [{openDialogWhenInit: false}])
        }, 1000);
    </script>

    <?php
    $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'HIDE' => true,
        'BUTTONS' => [
            [
                'TYPE' => 'save',
                'ONCLICK' => 'AccessRights.sendActionRequest()',

            ],
            [
                'TYPE' => 'cancel',
                'ONCLICK' => 'AccessRights.fireEventReset()'
            ],
        ],
    ]);
    ?>
<?php
endif;