var CkEditorImageBrowser = {};

CkEditorImageBrowser.folders = [];
CkEditorImageBrowser.images = {}; //folder => list of images
CkEditorImageBrowser.ckFunctionNum = null;

CkEditorImageBrowser.init = function () {
	CkEditorImageBrowser.ckFunctionNum = CkEditorImageBrowser.getQueryStringParam('CKEditorFuncNum');

	CkEditorImageBrowser.initEventHandlers();

	CkEditorImageBrowser.loadData(decodeURIComponent(CkEditorImageBrowser.getQueryStringParam('listUrl')), function () {
		CkEditorImageBrowser.initFolderSwitcher();
	});
};

CkEditorImageBrowser.loadData = function (url, onLoaded) {
	CkEditorImageBrowser.folders = [];
	CkEditorImageBrowser.images = {};

	$.getJSON(url, function (list) {
		$.each(list, function (_idx, item) {
			if (typeof(item.folder) === 'undefined') {
				item.folder = 'Images';
			}

			CkEditorImageBrowser.ensureFolderCreated(item.folder);
			CkEditorImageBrowser.addImage(item.folder, item.image, item.thumb, item.alt, item.title);
		});

		onLoaded();
	});
};

CkEditorImageBrowser.ensureFolderCreated = function (folderName) {
	if (CkEditorImageBrowser.folders.indexOf(folderName) === -1) {
		CkEditorImageBrowser.folders.push(folderName);
		CkEditorImageBrowser.images[folderName] = [];
	}
};

CkEditorImageBrowser.addImage = function (folderName, imageUrl, thumbUrl, imageAlt, imageTitle) {
	CkEditorImageBrowser.images[folderName].push({
		"imageUrl": imageUrl,
		"thumbUrl": thumbUrl,
                "imageTitle": imageTitle,
                "imageAlt": imageAlt
	});
};

CkEditorImageBrowser.initFolderSwitcher = function () {
	var $switcher = $('#js-folder-switcher');

	$switcher.find('option').remove();

	$.each(CkEditorImageBrowser.folders, function (idx, folderName) {
		var $option = $('<option></option>').val(idx).text(folderName);
		$option.appendTo($switcher);
	});

	if (CkEditorImageBrowser.folders.length === 1) {
		$switcher.hide();
	}

	$switcher.trigger("change");
};

CkEditorImageBrowser.renderImagesForFolder = function (folderName) {
	var images = CkEditorImageBrowser.images[folderName],
		templateHtml = $('#js-template-image').html(),
		$imagesContainer = $('#js-images-container');

	$imagesContainer.html('');

	$.each(images, function (idx, imageData) {
		var html = templateHtml;
		html = html.replace('%imageUrl%', imageData.imageUrl);
		html = html.replace('%thumbUrl%', imageData.thumbUrl);
		html = html.replace('%imageTitle%', imageData.imageTitle);
		html = html.replace('%imageAlt%', imageData.imageAlt);

		var $item = $($.parseHTML(html));

		$imagesContainer.append($item);
	});
};

CkEditorImageBrowser.initEventHandlers = function () {
	$('#js-folder-switcher').change(function () {
		var idx = parseInt($(this).val(), 10),
			folderName = CkEditorImageBrowser.folders[idx];

		CkEditorImageBrowser.renderImagesForFolder(folderName);
	});

	$(document).on('click', '.js-image-link', function () {
		var imageUrl = $(this).data('url');
		var imageTitle = $(this).data('title');
		var imageAlt = $(this).data('alt');
                
		window.opener.CKEDITOR.tools.callFunction(CkEditorImageBrowser.ckFunctionNum, imageUrl, 
    function() {
        
  // Get the reference to a dialog window.
  var element, dialog = this.getDialog();
  // Check if this is the Image dialog window.
  if (dialog.getName() == 'image') {
    // Get the reference to a text field that holds the "alt" attribute.
    element = dialog.getContentElement( 'info', 'txtAlt' );
    // Assign the new value.
    if ( element )
      element.setValue( imageAlt);
  
      element = dialog.getContentElement( 'advanced', 'txtGenTitle' );
    // Assign the new value.
    if ( element )
      element.setValue( imageTitle);
  }
  
  // Return false to stop further execution - in such case CKEditor will ignore the second argument (fileUrl)
  // and the onSelect function assigned to a button that called the file browser (if defined).
 // return false;
}        
        
    );
		window.close();
	});
};

CkEditorImageBrowser.getQueryStringParam = function (name) {
	var regex = new RegExp('[\?|&]' + name + '=(.+?)[\&|$]'),
		result = window.location.search.match(regex);

	return (result && result.length > 1 ? result[1] : null);
};
