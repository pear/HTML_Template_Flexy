<?php echo '<?xml version="1.0"?>';?>
<?php echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';?>

<window id="wndProductAdd" title="New Product" debug="false" onload="window._sizeToContent();" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	
	<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->baseURL,'baseurl',true);echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
</script>
	<script type="application/x-javascript" src="js/common.js"></script>
	<script type="application/x-javascript" src="../searchjs.php"></script>
	<script type="application/x-javascript" src="js/catctrl.js"></script>
	<script type="application/x-javascript">
		function productAddApply() {
			req = new phpRequest(URI_CONTROL + "/New/product");
			req.add("product_category", get_value("listProdCat"));
			req.add("item_category", get_value("listItemCat"));
			req.add("item_subcategory", get_value("listItemSubCat"));
			req.add("supplier_id", get_value("listSupplier"));
			req.add("supplier_model_numb", get_value("txtSupModelNo"));
			req.add("article", get_value("txtArtDescr"));
			req.add("material", get_value("txtMaterial"));
			req.add("color", get_value("txtColor"));
			req.add("origin_country", get_value("txtCountry"));
			req.add("dispatch_port", get_value("txtPort"));
			req.add("packing", get_value("txtPacking"));
			req.add("qty_20ft_container", get_value("txtPacking20"));
			req.add("qty_40ft_container", get_value("txtPacking40"));
			req.add("qty_40ft_hq_container", get_value("txtPacking40hq"));
			req.add("minimum_order_qty", get_value("txtMinOrderQty"));
			req.add("container_id", get_value("listContType"));
			req.add("delivery_time", get_value("listDeliveryYear") + "-" + get_value("listDeliveryMonth") + "-" + get_value("listDeliveryDay"));
			req.add("payment", get_value("txtPaymentCond"));
			req.add("unit_price", get_value("txtUnitPrice"));
			req.add("product_id", get_value("hProductId"));
			req.execute();
			self.window.close();
		}
	</script>
	
	<commandset>
		<command id="cmdBtnCancel" oncommand="self.window.close();" />
		<command id="cmdBtnApply" oncommand="productAddApply();" />
	</commandset>
	
	<?php echo $this->elements['form']->toHtmlnoClose();?>
	<groupbox flex="1">
		<caption label="New Product" />
		<hbox>
			<html:table border="0" cellspacing="0" cellpadding="0">
				<html:tr>
					<html:td><label style="text-align: left;" control="listProdCat" value="Product Category" />
					<html:td>
						<?php echo $this->elements['listProdCat']->toHtml();?>
					
				
				<html:tr>
					<html:td><label style="text-align: left;" control="listItemCat" value="Item Category" />
					<html:td>
						<?php echo $this->elements['listItemCat']->toHtml();?>
					
				
				<html:tr>
					<html:td><label style="text-align: left;" control="listItemSubCat" value="Item Subcategory" />
					<html:td>
						<?php echo $this->elements['listItemSubCat']->toHtml();?>
					
				
				<html:tr>
					<html:td><label style="text-align: left;" control="listSupplier" value="Supplier" />
					<html:td>
					
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtSupModelNo" value="Supplier Model No." />
					<html:td><?php echo $this->elements['txtSupModelNo']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtArtDescr" value="Article Description" />
					<html:td><?php echo $this->elements['txtArtDescr']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtMaterial" value="Material" />
					<html:td><?php echo $this->elements['txtMaterial']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtColor" value="Color" />
					<html:td><?php echo $this->elements['txtColor']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtCountry" value="Country of origin" />
					<html:td><?php echo $this->elements['txtCountry']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtPort" value="Port of dispatch" />
					<html:td><?php echo $this->elements['txtPort']->toHtml();?>
				
				<html:tr>
					<html:td><label style="text-align: left;" control="txtPacking" value="Packing" />
					<html:td><?php echo $this->elements['txtPacking']->toHtml();?>
				
			
			
			<vbox>
				<groupbox>
					<caption label="Packing per container" />
					<html:table border="0" cellspacing="0" cellpadding="0">
						<html:tr>
							<html:td><label control="txtPacking20" value="20' container" />
							<html:td><?php echo $this->elements['txtPacking20']->toHtml();?>
						
						<html:tr>
							<html:td><label control="txtPacking40" value="40' container" />
							<html:td><?php echo $this->elements['txtPacking40']->toHtml();?>
						
						<html:tr>
							<html:td><label control="txtPacking40hq" value="40' HQ container" />
							<html:td><?php echo $this->elements['txtPacking40hq']->toHtml();?>
						
					
				</groupbox>
				
				<html:table border="0" cellspacing="0" cellpadding="0">
					<html:tr>
						<html:td><label control="txtMinOrderQty" value="Minimum Order Qty" />
						<html:td><?php echo $this->elements['txtMinOrderQty']->toHtml();?>
					
					<html:tr>
						<html:td><label control="listContType" value="Container Type" />
						<html:td>
							<?php echo $this->elements['listContType']->toHtml();?>
						
					
					<html:tr>
						<html:td><label control="listDeliveryYear" value="Delivery time" />
						<html:td>
							<?php echo $this->elements['listDeliveryYear']->toHtml();?>
							<?php echo $this->elements['listDeliveryMonth']->toHtml();?>
							<?php echo $this->elements['listDeliveryDay']->toHtml();?>
						
					
					<html:tr>
						<html:td><label control="txtPaymentCond" value="Payment Conditions" />
						<html:td><?php echo $this->elements['txtPaymentCond']->toHtml();?>
					
					<html:tr>
						<html:td><label control="txtUnitPrice" value="Unit Price" />
						<html:td><?php echo $this->elements['txtUnitPrice']->toHtml();?>
					
					<html:tr>
						<html:td><label control="filePicture" value="Picture" />
						<html:td><?php echo $this->elements['filePicture']->toHtml();?>
					
				
			</vbox>
		</hbox>
	</groupbox>
	</html:form>
	
	<description id="hProductId" collapsed="true" value="<?php echo htmlspecialchars($t->product_id);?>" />
	<hbox>
		<spacer flex="1" />
		<button id="btnAddProdApply" label="OK" command="cmdBtnApply" />
		<button id="btnAddProdCancel" label="Cancel" command="cmdBtnCancel" />
	</hbox>
	
</window>
