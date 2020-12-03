/**
 * @file 附件发布插件
 */

(function()
{
	var addonDialog = function( editor, dialogType )
	{
		return {
			title : '附件发布',
			minWidth : 420,
			minHeight : 160,
			onOk : function()
			{
				var addonUrl = this.getValueOf( 'Link', 'txtUrl' );
				var addonTitle = this.getValueOf( 'Link', 'txtTitle');
				var tempvar='<table width="450">\r    <tbody>\r        <tr>\r            <td width="20" height="30"><a target="_blank" href="'+addonUrl+'"><img border="0" align="middle" src="/plus/img/addon.gif" alt="" /></a></td>\r            <td><a target="_blank" href="'+addonUrl+'"><u>'+addonTitle+'</u></a></td>\r        </tr>\r    </tbody>\r</table>';
				editor.insertHtml(tempvar);
				
			},
			contents : [
				{
					id : 'Link',
					label : '附件',
					padding : 0,
					type : 'vbox',
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									id : 'txtTitle',
									type : 'text',
									label : '附件标题',
									style : 'width: 60%',
									'default' : ''
								},
								{
									id : 'txtUrl',
									type : 'text',
									label : '选择附件',
									style : 'width: 100%',
									'default' : ''
								},
								{
									type : 'button',
									id : 'browse',
									filebrowser :
									{
										action : 'Browse',
										target: 'Link:txtUrl',
										url: '../include/dialog/select_soft.php'
									},
									style : 'float:right',
									hidden : true,
									label : editor.lang.common.browseServer
								}
							]
						}
					]
				}
			]
		};
	};

	CKEDITOR.dialog.add( 'addon', function( editor )
		{
			return addonDialog( editor, 'addon' );
		});
})();
