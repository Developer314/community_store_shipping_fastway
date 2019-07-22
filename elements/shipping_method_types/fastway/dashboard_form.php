<?php
defined('C5_EXECUTE') or die("Access Denied.");
extract($vars);
$franchisees = array('' => 'Select Franchisee');
?>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('fastway_api_key', t("API Key")); ?>
        <div class="input-group">
            <?= $form->text('fastway_api_key', $smtm->getAPIKey()); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('country_code', t("Country")); ?>
        <div class="input-group">
            <?= $form->select('country_code', $countries, $smtm->getCountryCode()); ?>
            <div class="loading"></div>
        </div>

    </div>
</div>

<div class="row" style="margin-bottom: 10px">
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('franchisee_code', t("Franchisee Code")) . $form->hidden('selected_franchisee_code', $smtm->getFranchiseeCode()); ?>
        <div class="input-group franchisee_code_outer">
            <?= $form->select('franchisee_code', $franchisees, $smtm->getFranchiseeCode()); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('surcharge', t("Surcharge(optional)")); ?>
        <div class="input-group">
            <?= $form->text('surcharge', $smtm->getSurcharge(), array('pattern' => "^\d{1,3}(,\d{3})*(\.\d+)?$", 'title' => 'Please Enter valid Currency format e.g: 00.00')); ?>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 10px">
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('fastway_satchel_name', t("Fastway Satchel Name")); ?>
        <div class="input-group">
            <?= $form->text('fastway_satchel_name', $smtm->getSatchelName() != '' ? $smtm->getSatchelName() : 'Fastway Satchel'); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('fastway_satchel_description', t("Fastway Satchel Description")); ?>
        <div class="input-group">
            <?= $form->text('fastway_satchel_description', $smtm->getSatchelDescription()); ?>
        </div>

    </div>
</div>
<div class="row" style="margin-bottom: 10px">
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('fastway_parcel_name', t("Fastway Parcel Name")); ?>
        <div class="input-group">
            <?= $form->text('fastway_parcel_name', $smtm->getParcelName() != '' ? $smtm->getParcelName() : 'Fastway Parcel'); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->label('fastway_parcel_description', t("Fastway Parcel Description")); ?>
        <div class="input-group">
            <?= $form->text('fastway_parcel_description', $smtm->getParcelDescription()); ?>
        </div>

    </div>
</div>

<script>
    $(document).ready(function () {
        getFranshisee();
        $('#country_code').change(function () {
            getFranshisee();
        });
    });

    function getFranshisee() {

        let franchisee_code = $('#franchisee_code').val() ? $('#franchisee_code').val() : $('#selected_franchisee_code').val();
        if ($('#country_code').val() > 0) {
            $('.franchisee_code_outer select').html('');
            if ($('#fastway_api_key').val() == '') {
                alert('API key required to proceed');
                $('#country_code').val('');
                return;
            }
            $('.loading').addClass('spinner');
            $.ajax({
                method: "POST",
                url: CCM_APPLICATION_URL + '/get_franchisee',
                data: {fastway_api_key: $('#fastway_api_key').val(), country_code: $('#country_code').val()}
            })
                .done(function (msg) {
                    $('.franchisee_code_outer').html(msg);
                    if (franchisee_code != '') {
                        $('#franchisee_code').val(franchisee_code);
                    }
                    $('.loading').removeClass('spinner');
                });

        }

    }
</script>
<style>
    @keyframes spinner {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner:before {
        content: '';
        box-sizing: border-box;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border-radius: 50%;
        border: 2px solid #ccc;
        border-top-color: #000;
        animation: spinner .6s linear infinite;
    }

    #country_code {
        display: inline-block;
        width: auto;
    }

    .spinner {
        float: right;
        width: 30px;
        position: relative;
        height: 30px;
        display: inline-block;
    }
</style>
