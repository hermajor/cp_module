<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
    "NAME" => GetMessage("CP_MODULE_ADD_NAME"),
    "DESCRIPTION" => GetMessage("CP_MODULE_ADD_DESC"),
    "PATH" => array(
        "ID" => "cp_component",
        "NAME" => "cp_component",
        "CHILD" => array(
            "ID" => "changepass_form",
            "NAME" => GetMessage("CP_MODULE_SECTION_NAME")
        )
    ),
);
?>

