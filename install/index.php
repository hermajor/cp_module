<?

global $MESS;

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
@include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install/index.php");

Class cp_module extends CModule
{
    var $MODULE_ID = "cp_module";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    function cp_module()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = GetMessage("CP_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("CP_MODULE_DESCRIPTION");
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/cp_module/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/cp");
        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        RegisterModule("cp_module");
        $APPLICATION->IncludeAdminFile(GetMessage("CP_MODULE_INSTALL"), $DOCUMENT_ROOT."/bitrix/modules/cp_module/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        UnRegisterModule("cp_module");
        $APPLICATION->IncludeAdminFile(GetMessage("CP_MODULE_UNINSTALL"), $DOCUMENT_ROOT."/bitrix/modules/cp_module/install/unstep.php");
    }

    function UnInstallEvents()
    {
        global $DB;
        $DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('CP_MODULE_PASSWORD_CHANGE')");
        $DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('CP_MODULE_PASSWORD_CHANGE')");
    }
}
?>