<?php
	
	//allow cross-domain requests
	header('Access-Control-Allow-Origin: *');

	//get the xform source
	$source = (isset($_FILES['xform']) && $_FILES['xform']['size'] > 0) 
		? $_FILES['xform']['tmp_name'] 
		: ( isset($_GET['xform'])
			? $_GET['xform'] : NULL );
	
	if ( $source ) {

		$xform = new DOMDocument();
		$form_xsl = new DOMDocument();
		$model_xsl = new DOMDocument();

		// load files
		$xform->load($source);
		$form_xsl->load('lib/xsl/openrosa2html5form_php5.xsl');
		$model_xsl->load('lib/xsl/openrosa2xmlmodel.xsl');

		// use libxml error handler
        libxml_use_internal_errors(true);

		// get HTML Form transformation result
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($form_xsl);
		$form = simplexml_load_string($proc->transformToXML($xform));

		// get XML Model transformation result
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($model_xsl);
		$model = simplexml_load_string($proc->transformToXML($xform));
		
		// combine the results
		$result = new SimpleXMLElement('<root>'.$model->model->asXML().$form->form->asXML().'</root>');
		
		// output result
		echo $result->asXML();
	} else {

		//output file input
		echo '<form action="" method="post" enctype="multipart/form-data">';
		echo '<label>OpenRosa XForm: <input type=file name="xform" /></label>';
		echo '<input type="submit" name="submit" value="Submit">';
		echo '</form>';
	}
?>
