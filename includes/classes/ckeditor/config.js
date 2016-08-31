/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

//fix bug in chrome not correct size < 310px - Deivisson
CKEDITOR.on('instanceReady', function (evt) {
    //editor
    var editor = evt.editor;

    //webkit not redraw iframe correctly when editor's width is < 310px (300px iframe + 10px paddings)
    if (CKEDITOR.env.webkit && parseInt(editor.config.width) < 310) {
        var iframe = document.getElementById('cke_contents_' + editor.name).firstChild;
        iframe.style.display = 'none';
        iframe.style.display = 'block';
    }
});

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.toolbar = 'Base';
	
	CKEDITOR.config.toolbar_Base =
	[
		['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','Bold','Italic','-','NumberedList','BulletedList','-','Outdent','Indent','-','Link','Unlink','-','Table','Image','SpecialChar']
	];
	
	config.toolbar = 'SOLICITA';
	
    config.removePlugins = 'elementspath';
	CKEDITOR.config.toolbar_SOLICITA = 
	[
        ['Copy','Paste','PasteText','PasteFromWord','NumberedList','BulletedList','Link','Unlink','Table','Image','Bold','Italic','TextColor','BGColor']
	] ;
	
	config.toolbar = 'SOLICITA_DET';
	CKEDITOR.config.toolbar_SOLICITA_DET = 
	[
       [],
	] ;

};


