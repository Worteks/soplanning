{* Smarty *}
<form class="form-horizontal" method="get" action="export_pdf.php" target="_blank">
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#PDFExport_orientation#} :</label>
		<div class="col-md-4">
			<select name="pdf_orientation" id="orientation" class="form-control">
				<option value="paysage" {if $pdf_orientation eq "paysage"}selected="selected"{/if}>{#PDFExport_orientation_paysage#}</option>
				<option value="portrait" {if $pdf_orientation eq "portrait"}selected="selected"{/if}>{#PDFExport_orientation_portrait#}</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#PDFExport_format#} :</label>
		<div class="col-md-4">
			<select name="pdf_format" id="format" class="form-control">
				<option value="A4" {if $pdf_format eq "A4"}selected="selected"{/if}>A4</option>
				<option value="A3" {if $pdf_format eq "A3"}selected="selected"{/if}>A3</option>
				<option value="A2" {if $pdf_format eq "A2"}selected="selected"{/if}>A2</option>
				<option value="A1" {if $pdf_format eq "A1"}selected="selected"{/if}>A1</option>
				<option value="A0" {if $pdf_format eq "A0"}selected="selected"{/if}>A0</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-3"></div>
		<div class="col-md-7">
			<input id="cb_inclure_recap" name="cb_inclure_recap" type="checkbox" value="1" />
			<label for="cb_inclure_recap" style="display:inline;font-weight:normal">{#pdf_inclure_recap#}</label>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<br>
			<input type="submit" class="btn btn-primary" value="{#winPeriode_valider#|xss_protect}" />
		</div>
	</div>
</form>