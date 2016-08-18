/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	config.height = 265;
	//禁止改变编辑器大小
	config.resize_enabled = false;
	
	//word粘贴不移除格式
	config.pasteFromWordRemoveFontStyles = false;
	config.pasteFromWordRemoveStyles = false;
	
	//是否使用完整的html编辑模式 如使用，其源码将包含：<html><body></body></html>等标签
	config.fullPage = true; 
	
	//默认中文
	config.defaultLanguage = 'zh-cn';
	
	//添加tab=4空格设置
	config.tabIndex = 0;
	config.tabSpaces = 4;
	
	//添加中文字体
	config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
	
	//允许工具栏收缩
	config.toolbarCanCollapse = true;
	 
	//自定义工具栏
	config.toolbar = 'TplToolbar';
	config.toolbar_TplToolbar =
	[
	 	[ 'Source' ],
	    [ 'Bold','Italic','Underline','-','RemoveFormat' ] ,
	    [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ],
	    [ 'Format','Font','FontSize'],
	    [ 'TextColor','BGColor'],
	    [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight' ],
	    [ 'Image','Table','HorizontalRule','Smiley' ] ,
	    [ 'Link','Unlink','Anchor' ]
	];
	
};
