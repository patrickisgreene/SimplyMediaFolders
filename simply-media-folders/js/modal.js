(function ($) {
	'use strict';

	const htmlTemplate = `
	<div id="smf">
    	<div id="smf-tree-wrap">
		    <div id="smf-tree"></div>
	    </div>
	</div>`;
	const folderTemplate = (node) => `
	<div class="smf-node-edit">
	    <input class="smf-edit-color" type="color" value="${node.color}" disabled />
	    <input class="smf-edit-name" value="${node.name}" disabled />
	</div>`;
	$(document).ready(() => {
		if (window.parent.wp.media !== undefined && window.parent.wp.media.view !== undefined) {
			var attachmentsBrowser = window.parent.wp.media.view.AttachmentsBrowser;
			window.parent.wp.media.view.AttachmentsBrowser = window.parent.wp.media.view.AttachmentsBrowser.extend({
				ready: function () {
					if(!this.views.parent.$el.hasClass("e-wp-media-elements-removed")) {
						if (!this.views.parent.$el.hasClass("hide-menu")) return;
					}
					attachmentsBrowser.prototype.ready.call(this);
					if($(`#smf`).length > 0) return;
					var frameMenu = this.views.parent.views.get(".media-frame-menu");

					frameMenu[0].views.add(new wp.media.View({
						el: htmlTemplate
					}));
					simplyMediaFoldersConfig.buildMediaFoldersTree(folderTemplate);
				}
			});
		}
	});
})(jQuery);
