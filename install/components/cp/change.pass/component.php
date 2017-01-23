<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$et = new CEventType;
$rsET = $et->GetList(Array("TYPE_ID" => "CP_MODULE_PASSWORD_CHANGE"));

    if (!$arET = $rsET->Fetch())
    {

        $rsSites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
        $siteID = $rsSites->arResult[0]['LID'];

        $eventID = $et->Add(array(
            "LID" => $siteID,
            "EVENT_NAME" => "CP_MODULE_PASSWORD_CHANGE",
            "NAME" => GetMessage("CP_MODULE_NAME"),
            "DESCRIPTION" => GetMessage("CP_MODULE_DESC")
        ));

        $emess = new CEventMessage;
        $arMessage = Array(
            "ACTIVE" => "Y", //флаг активности почтового шаблона: "Y" - активен; "N" - не активен;
            "LID" => $siteID, //идентификатор сайта;
            "EVENT_NAME" => "CP_MODULE_PASSWORD_CHANGE", //идентификатор типа почтового события;
            "EMAIL_FROM" => "#EMAIL_FROM#", //поле "From" ("Откуда");
            "EMAIL_TO" => "#EMAIL_TO#", //поле "To" ("Куда");
            "SUBJECT" => GetMessage("CP_MODULE_EVENT_SUBJECT"), //заголовок сообщения;
            "BODY_TYPE" => "text", //тип тела почтового сообщения: "text" - текст; "html" - HTML;
            "MESSAGE" => GetMessage("CP_MODULE_EVENT_MESSAGE"), //тело почтового сообщения.
        );

        if (!$emess->Add($arMessage))
        {
            $arResult["FORM_ERRORS"]["MESS_ADD"]["MESSAGE"] = $emess->LAST_ERROR;
        }

    }

$data = $_POST;
    if (isset($data['button'])) {
        $email = trim(htmlspecialchars($data['email']));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $rsUsers = CUser::GetList($by = "", $order = "", array('=EMAIL' => $email));
            $id_user = $rsUsers->result->fetch_assoc();

            if ($id_user['ID'] && $email != '') {

                $rsSites = CSite::GetList($by = "sort", $order = "desc", Array("ACTIVE" => "Y"));
                $siteID = $rsSites->arResult[0]['LID'];

                $arGroups = CUser::GetUserGroup($id_user['ID']);
                $adminGroup = 1;

                $key = array_search($adminGroup, $arGroups);

                $chars = array(
                    'abcdefghijklnmopqrstuvwxyz',
                    'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
                    '0123456789',
                );
                if (is_numeric($key)) {
                    $chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
                }

                $length = rand(7, 10);
                $new_pass = randString($length, $chars);

                $user = new CUser;
                $fields = Array(
                    "PASSWORD" => $new_pass,
                    "CONFIRM_PASSWORD" => $new_pass,
                );
                $user->Update($id_user['ID'], $fields);
                $strError .= $user->LAST_ERROR;

                if ($strError != ''){
                    echo $strError;
                } else {
                    $arFields = array(
                        "MESSAGE" => GetMessage("CP_MODULE_EVENT_NEW_MESSAGE") . $new_pass,
                        "EMAIL_TO" => $email
                    );
                    CEvent::Send("CP_MODULE_PASSWORD_CHANGE", $siteID, $arFields);

                    echo GetMessage("CP_MODULE_OK");
                }

            } else {
                echo GetMessage("CP_MODULE_EMAIL_NOT_FOUND");
                $this->IncludeComponentTemplate();
            }

        }else{
            echo "Вы пытаетесь ввести некорректный e-mail адрес или пустую строку";
            $this->IncludeComponentTemplate();
        }
    
    }else{
        echo GetMessage("CP_MODULE_INFO");
        $this->IncludeComponentTemplate();

    }
?>
