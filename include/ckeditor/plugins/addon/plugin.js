/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Image plugin
 */

CKEDITOR.plugins.add( 'addon',
{
	init : function( editor )
	{
		var pluginName = 'addon';

		// Register the dialog.
		CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/addon.js' );

		// Register the command.
		editor.addCommand( pluginName, new CKEDITOR.dialogCommand( pluginName ) );

		// Register the toolbar button.
		editor.ui.addButton( 'Addon',
			{
				label : '附件',
				icon : 'http://desdevcms.com/images/addon.gif',
				command : pluginName
			});
	}
} );
