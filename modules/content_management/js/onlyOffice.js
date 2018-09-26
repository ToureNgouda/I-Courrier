console.log('test onlyOffice');

//see documentation from renderjs.org

//the class
rJS(window)
	.ready(function launchrJS() {
		//method calling on instanciate the gadget
		//console.log('gadget OK');
		//console.log(this.element);
		iframe = this.element.querySelector('iframe');
		//console.log(iframe);
		if (iframe) {
			iframe.style.width = '100%';
			iframe.style.height = '80vh';
		}
		
	})
	.declareService(function callEditor() {
		var editorGadget;
		//method calling on the loading gadget 
		//console.log('declareService callEditor OK');
		//use the instance of the gadget
		return this.getDeclaredGadget('editor')
			.push(function (result) {
				editorGadget = result;
				//call ajax method for cloudooo
				return jIO.util.ajax({
					url: 'index.php?page=test_cloudooo_ajax&module=content_management&display=false',
					type: 'POST',
					dataType: 'blob'
				});
			})
			.push(function (resultAjax) {
				//resultAjax.target.response -> body de la réponse ajax en format blob
				//console.log("resultAjax.target.response", resultAjax.target.response);
				//resultAjaxDataUrl = jIO.util.readBlobAsDataURL(resultAjax.target.response);
				return jIO.util.readBlobAsDataURL(resultAjax.target.response);
			})
			.push(function (resultAjaxDataUrl) {
				console.log("resultAjaxDataUrl", resultAjaxDataUrl);
				return editorGadget.render({
					jio_key: 'test',
					value: resultAjaxDataUrl.target.result,
				});
			})
			;
		
		//toutes les méthodes utilisées sur editorGadget me renverront une promesse
		//mettre systématiquement un return devant chaque fonction imbriquée
		//renderJS ne permet l'accès aux gadget de ses fils et pas en dessous
		//mécanisme d'acquisition permet de récupérer des méthodes d'un autre gadget
		//-> declareAcquiredMethod
	})
	.allowPublicAcquisition('setFillStyle', function (a) {
		return {
			height: '1OO%',
			width: '1OO%'
		};
	})
	.onEvent('click', function (evt) {
		if (evt.target.id === 'saveEditor') {
			return this.getDeclaredGadget('editor')
				.push(function (editorGadget) {
					//console.log("resultContentDoc", editorGadget.getContent());
					return editorGadget.getContent();
				})
				.push(function (getResultContentDoc) {
					//console.log('getResultContentDoc', getResultContentDoc);
					//console.log('jIO.util.dataURItoBlob(getResultContentDoc)', jIO.util.dataURItoBlob(getResultContentDoc.text_content));
					return jIO.util.dataURItoBlob(getResultContentDoc.text_content);
				})
				.push(function (ajaxYFormatToOffice) {
					//call ajax method for cloudooo
					var form_data = new FormData();
					form_data.append('file', ajaxYFormatToOffice);
					return jIO.util.ajax({
						url: 'index.php?page=test_cloudooo_ajax_final&module=content_management&display=false',
						type: 'POST',
						dataType: 'blob',
						data: form_data
					});
				})
				.push(function (locationToPdf) {
					//console.log("locationToPdf", locationToPdf);
					return jIO.util.readBlobAsDataURL(locationToPdf.target.response);
					
				})
				.push(function (pathTolocacationToPdf) {
					//console.log('pathTolocacationToPdf', pathTolocacationToPdf);
					//console.log('pathTolocacationToPdf', pathTolocacationToPdf.target.result);
					finalPdfButton =  document.getElementById('openPDF');
					//console.log(finalPdfButton);
					if (finalPdfButton) {
						finalPdfButton.disabled = false;
					}
					
				})
				;
		}
	})
;