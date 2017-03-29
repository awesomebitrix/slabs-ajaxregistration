<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Context;

$request = Context::getCurrent()->getRequest();
$reqAction = $request->getPost('action');
$redirect = $request->get('redirect');

if ( $request->isAjaxRequest() && $reqAction=="captcha" ) {
    $APPLICATION->RestartBuffer();
    echo json_encode($APPLICATION->CaptchaGetCode());
    die();
}

?>
<div class="registration">
    <?
    if( $USER->IsAuthorized() ){
        if ( $request->isAjaxRequest() ){
            $APPLICATION->RestartBuffer();

            if( intval($arResult["VALUES"]["USER_ID"]) && $arResult["VALUES"]["USER_ID"] > 0 )
                echo "Спасибо за регистрацию".($redirect <>'' ?' <a href="'.$redirect.'">вернуться</a>.':'.');

            die();
        }else
            echo Loc::getMessage('MAIN_REGISTER_AUTH');
    }else{?>
        <?if ( $request->isAjaxRequest() ){?>
            <?$APPLICATION->RestartBuffer();?>
            <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" class="form-ajax" enctype="multipart/form-data">
                <input type="hidden" name="register_submit_button" value="Y" >
                <?if( $redirect <> '' && $arParams["USE_BACKURL"]=="Y" ) {
                    echo '<input type="hidden" name="redirect" value="' . $redirect . '" />';
                }
                ?>
                <?foreach ($arResult["SHOW_FIELDS"] as $FIELD){?>
                    <div class="field <?=($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD]=="Y"?"required":"")?>">
                        <?switch ($FIELD) {
                            case "PASSWORD":
                            case "CONFIRM_PASSWORD":?>
                                <input
                                    size="30"
                                    class="<?=(!empty($arResult["ERRORS"][$FIELD])?"error":"")?>"
                                    type="password"
                                    name="REGISTER[<?=$FIELD?>]"
                                    value="<?=$arResult["VALUES"][$FIELD]?>"
                                    placeholder="<?=Loc::getMessage('REGISTER_FIELD_'.$FIELD);?>"
                                    alt=""
                                    autocomplete="off"
                                >
                                <?break;
                            default:?>
                                <input size="30" class="<?=(!empty($arResult["ERRORS"][$FIELD])?"error":"")?>" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" placeholder="<?=Loc::getMessage('REGISTER_FIELD_'.$FIELD);?>" >
                                <?break;
                        }?>
                    </div>
                <?}?>
                <?if ($arResult["USE_CAPTCHA"] == "Y"){?>
                <div class="field captcha">
                    <input type="text" name="captcha_word" maxlength="50" value="" class="<?=(!empty($arResult["ERRORS"]["CAPTCHA"])?"error":"")?>"/>
                    <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>"
                         width="180"
                         height="40"
                         alt="CAPTCHA"
                         class="sl-button-ajax"
                         data-action="captcha"
                         data-url="<?=$APPLICATION->GetCurPage()?>"
                    >
                </div>
                <?}?>
                <input type="submit" value="<?=Loc::getMessage("AUTH_REGISTER")?>" >
            </form>
            <?die()?>
        <?}else{?>
            <span class="sl-button-ajax" data-url"<?=$APPLICATION->GetCurPage()?>"><?=Loc::getMessage('AUTH_REGISTER');?></span>

            <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                    </div>
                </div>
            </div>

        <?}?>
    <?}?>
</div>