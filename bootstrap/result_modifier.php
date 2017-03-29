<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\IO\Path,
    \Bitrix\Main\Localization\Loc;

$arMESS = [];
$EXTENDED_ERROR = [];
$ERRORS_LIST = [];
$md5="";
$arFile = [
    $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/bitrix/main.register/lang/ru/component.php',
    $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/lang/ru/classes/general/user.php',
];

foreach ($arFile as $path)
    $md5 .= md5_file($path)."_";

$cache_id = md5($md5);
$cache_dir = "/slabs.main.register";

$obCache = new CPHPCache;
// Кэш на 30 дней
if($obCache->InitCache(86400*30, $cache_id, $cache_dir)) {
    $arMESS = $obCache->GetVars();
}elseif( $obCache->StartDataCache() ) {

    function loadMessages ($path){
        $File = Path::normalize($path);
        $MESS = [];

        if(file_exists($File))
            include($File);

        return $MESS;
    }

    foreach ($arFile as $path)
        $arMESS = array_merge($arMESS,loadMessages($path));

    $ERRORS_LIST=[
        "MIN_LOGIN"                                     => "LOGIN",
        "LOGIN_WHITESPACE"                              => "LOGIN",
        "USER_EXIST"                                    => "LOGIN",
        "MIN_PASSWORD1"                                 => "PASSWORD",
        "MAIN_FUNCTION_REGISTER_PASSWORD_LENGTH"        => "PASSWORD",
        "MAIN_FUNCTION_REGISTER_PASSWORD_UPPERCASE"     => "PASSWORD",
        "MAIN_FUNCTION_REGISTER_PASSWORD_LOWERCASE"     => "PASSWORD",
        "MAIN_FUNCTION_REGISTER_PASSWORD_DIGITS"        => "PASSWORD",
        "MAIN_FUNCTION_REGISTER_PASSWORD_PUNCTUATION"   => "PASSWORD",
        "WRONG_CONFIRMATION"                            => "CONFIRM_PASSWORD",
        "WRONG_EMAIL"                                   => "EMAIL",
        "REGISTER_USER_WITH_EMAIL_EXIST"                => "EMAIL",
        "REGISTER_WRONG_CAPTCHA"                        => "CAPTCHA",
    ];

    $obCache->EndDataCache($arMESS);
}

if( is_array($arResult["ERRORS"]) && !empty($arResult["ERRORS"][0]) ){
    $arExtError = explode("<br>",$arResult["ERRORS"][0]);

    if ( empty(count($arExtError)-1) )
        array_pop($extError);

    foreach ($ERRORS_LIST as $key=>$ERROR){
        $strError = $arMESS[$key];
        $strError = preg_replace_callback(
            '/#[A-Z]+#/',
            function($match) use ($arResult,$ERRORS_LIST,$key){
                $matchReplace = str_replace("#","",$match[0]);
                if ( $match[0] == "#LENGTH#" ){
                    return $arResult["GROUP_POLICY"][$ERRORS_LIST[$key]."_".$matchReplace];
                }else
                    return $arResult["VALUES"][$matchReplace];
            },
            $strError
        );

        if( in_array($strError,$arExtError) )
            $EXTENDED_ERROR[$ERROR] = $strError;
    }

    $arResult["ERRORS"] = [];
    $arResult["ERRORS"] = $EXTENDED_ERROR;
}elseif( is_array($arResult["ERRORS"]) && empty($arResult["ERRORS"][0]) ){
    foreach ($arResult["ERRORS"] as $key => $ERROR)
        $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".Loc::getMessage("REGISTER_FIELD_".$key)."&quot;", $ERROR);
}

unset($ERROR,$EXTENDED_ERROR,$ERRORS_LIST,$arExtError,$arMESS,$key);