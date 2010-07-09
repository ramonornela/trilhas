/**
 * @requires Xquared.js
 * @requires Browser.js
 * @requires Editor.js
 * @requires plugin/Base.js
 * @requires ui/Control.js
 */
xq.plugin.FileGalleryPlugin = xq.Class(xq.plugin.Base,
	/**
	 * @name xq.plugin.FileUploadPlugin
	 * @lends xq.plugin.FileUploadPlugin.prototype
	 * @extends xq.plugin.Base
	 * @constructor
	 */
	{
	onAfterLoad: function(xed) {
		xed.config.defaultToolbarButtonGroups.insert.push(
			{className:"image", title:"Upload Image", handler:"runElementMeio('file/folder', 'file-folder');"}
		);
	}
});