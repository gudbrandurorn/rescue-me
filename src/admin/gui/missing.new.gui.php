<?
    use RescueMe\Locale;    
    
?>
<h3 class="no-wrap"><?=_("Start sporing")?></h3>
<?php if(isset($_ROUTER['error'])) { ?>
	<div class="alert alert-error">
		<strong>En feil oppsto!</strong><br />
		<?= $_ROUTER['error'] ?>
	</div>

<? } elseif(modules_exists("RescueMe\SMS\Provider")) { ?>

<form method="post">
    
    
	<fieldset class="new-missing pull-left" style="margin-right: 2em;">
		<legend><?=_('Den savnede')?></legend>

		<label for="m_name"><?=_('Navn på sporing')?></label>
		<input class="input-block-level" type="text" id="m_name" name="m_name" placeholder="Sted, landsdel, eller savnedes navn" autofocus required>

        <div class="row-fluid">
            <div class="span4">
                <label for="m_mobile">Land-kode</label>
                <select class="input-block-level" id="m_mobile_country" name="m_mobile_country" placeholder="Velg land" required>
                    <?= insert_options(Locale::getCountryNames(), Locale::getCurrentCountryCode()); ?>
                </select>
            </div>
            <div class="span8">
                <label for="m_mobile">Savnedes mobilnummer</label>
                <input class="input-block-level" type="tel" id="m_mobile" name="m_mobile" placeholder="Kun siffer, ingen mellomrom" required pattern="[0-9]*">
            </div>
        </div>
                
	</fieldset>
    
	<fieldset class="new-missing">
		<legend><?=_('Rapporter til')?></legend>

        <div class="row-fluid">
            <div class="span4">
                <label for="mb_mobile_country">Land-kode</label>
                <select class="input-block-level" id="mb_mobile_country" name="mb_mobile_country" placeholder="Velg land" required>
                    <?= insert_options(Locale::getCountryNames(), $user->mobile_country); ?>
                </select>
            </div>
            <div class="span8">
                <label for="m_mobile"><?=_('Mobilnummer')?></label>
                <input class="input-block-level" type="tel" id="m_mobile" name="mb_mobile" value="<?=$user->mobile?>" placeholder="Kun siffer, ingen mellomrom" required pattern="[0-9]*">
            </div>
        </div>
        
	</fieldset>
    
	<div class="clearfix"></div>
    
	<fieldset class="new-missing pull-left">
		<legend><?=_('SMS tekst')?></legend>

        <div class="row-fluid">
            <textarea class="field span12" id="sms_text" name="sms_text" required><?=SMS_TEXT?></textarea>
        </div>

        <div class="alert alert-info" style="position: relative;">
            <div> 
                <strong>RescueMe sender en SMS til den savnede når sporing opprettes</strong>. 
                <span style="color: red;">Husk å sette inn <span class="label">%LINK%</span> 
                slik at RescueMe kan sette inn med riktig lenke</span>.
            </div>
            <button type="button" data-toggle="readmore" class="toggle btn btn-mini btn-info"
                    style="position: absolute; right: 0; bottom: 0;">Mer...</button>
            <div id="readmore" style="display:none;">
                <h4>Standard</h4>
                <div class="alert"><?= SMS_TEXT ?></div>
                <h4>Sporingsside</h4>
                <p>Når brukeren trykker på lenken åpnes en nettside som vil forsøke å posisjonerer 
                    mobiltelefonen. Brukeren må godkjenne deling av posisjon i nettleseren før posisjonen 
                    kan bestemmes.
                </p><p>
                    <strong>Lastetid</strong>
                    <br />
                    Nettsiden er komprimert (1.8KB). Det burde ta mindre enn ett sekund på dårlig 
                    mobilnett (2G) å laste den ned. Det er likevel viktig at brukeren er tålmodig, og venter 
                    lengre enn dette hvis siden ikke åpnes.
                </p><p>
                    <strong>Gjentatt posisjonering</strong>
                    <br />
                    Hvis posisjonen er unøyaktig, vil nettsiden vente til posisjon med ønsket nøyaktighet 
                    er funnet, eller maksimum ventetid er nådd. En nedtelling vises mens dette foregår. 
                    Siste posisjon vises også til brukeren, slik at denne kan leses opp på telefonen, eller 
                    sendes på SMS (be brukeren klikke på linken bak posisjonen).
                <p/><p>
                    Ønsket nøyaktighet (location.desired.accuracy), maksimum ventetid 
                    (location.max.wait) og maximum alder på gammel posisjon (location.max.age) 
                    kan konfigureres på siden<a href="<?=ADMIN_URI?>setup#general">Oppsett</a>.
                <p/><p>
                    Alle sporinger er tilgjengelig på <a href="<?=ADMIN_URI?>/missing/list">admin/missing/list</a>.
                <p/>

            </div>
        </div>
        
	</fieldset>

    
	<div class="clearfix"></div>
    
	<fieldset class="new-missing pull-left">
        <div class="row-fluid">
            <button type="submit" class="btn btn-success span3"><?=_('Opprett')?></button>
            <div class="span4">
                <select id="m_type" name="m_type" class="input-block-level" >
                    <? insert_options(RescueMe\Operation::titles(), 'trace'); ?>
                </select>            
            </div>
        </div>
    </fieldset>

</form>

<? } ?>
