
(function ($) {
	'use strict';
	$(function () {
		const htmlTemplate = `
<div id="smf">
    <div id="smf-header">
        <a id="smf-settings">⋮</a>
	    <div id="smf-create-wrapper">
		    <input id="smf-create-color" type="color" value="${simplyMediaFoldersConfig.default_color}" />
		    <input id="smf-create-name" placeholder="Name" />
			</div>
			<a id="smf-add">
		    <span id="smf-add-icon" style="position:absolute">+</span>
			<span id="smf-add-spinner" class="spinner"></span>
		</a>
    </div>
    <div id="smf-settings-dialog" class="card hidden">
     <form>
	    <div class="smf-form-group">
	        <label>Filter</label>
	        <fieldset>
	           <label>Enable Filter</label>
			   <div style="display:flex">
    			   <span id="smf-enable-filter-spinner" class="spinner"></span>
	               <input id="smf-enable-filter" type="checkbox" ${simplyMediaFoldersConfig.filter_enabled ? 'checked' : null} />
				</div>
		    </fieldset>
	    </div>
	    <div class="smf-form-group">
	      <label>Appearance</label>
	      <fieldset>
	        <label>Default Color</label>
			<div style="display:flex">
			    <span id="smf-default-color-spinner" class="spinner"></span>
    	        <input id="smf-default-color" type="color" value="${simplyMediaFoldersConfig.default_color}" />
			</div>
	      </fieldset>
		  <fieldset>
	           <label>Auto Open</label>
    		  <div style="display:flex">
    			<span id="smf-auto-open-spinner" class="spinner"></span>
	            <input id="smf-auto-open" type="number" value="${simplyMediaFoldersConfig.auto_open ?? 0}" />
			  </div>
		  </fieldset>
	    </div>
	 </form> 
    </div>
    <div id="smf-tree-wrap">
        <div id="smf-tree"></div>
    </div>
</div>`;

		const folderTemplate = (node) => `
<div class="smf-node-edit" data-node-id="${node.id}" data-parent-id="${node.parent_id}">
  <div style="display:flex;align-items:center">
	<input class="smf-edit-color" type="color" value="${node.color}" disabled />
	<input class="smf-edit-name" value="${node.name}" disabled />
  </div>
	<div class="smf-node-actions">
      <div id="smf-edit-actions-${node.id}" class="smf-node-edit-actions">
        <a class="smf-node-button smf-node-edit-start">✎</a>
        <a class="smf-node-button smf-node-edit-delete">
			<span class="smf-node-icon">🗑</span>
        	<span class="spinner smf-node-edit-delete-spinner"></span>
		</a>
	  </div>
	  <div id="smf-save-actions-${node.id}" class="smf-node-actions smf-node-save-actions">
    	<a class="node-button smf-node-save-save">
		    <span class="smf-node-icon">🖫</span>
		    <span class="spinner smf-node-save-save-spinner"></span>
		</a>
    	<a class="smf-node-button smf-node-save-cancel">✗</a>
	  </div>
	</div>
</div>
`;
		function onCreateLi(node, li) {
			li.find('.smf-node-edit-delete').click((event) => {
				event.stopPropagation();
				$(`#smf-edit-actions-${node.id} > .smf-node-edit-delete > .spinner`).addClass('smf-active');
				$(`#smf-edit-actions-${node.id} > .smf-node-edit-delete > .smf-node-icon`).css('opacity', '0%');
				simplyMediaFoldersConfig.ajaxRequest(
					{ id: node.id, action: 'simply_media_folders_delete' },
					() => {
						node.children.forEach((child) => {
                            $('#smf-tree').tree('moveNode', child, $('#smf-tree').tree('getNodeById', node.parent_id), 'inside');
						});
						$('#smf-tree').tree('removeNode', node);
					}
				);
			});
			li.find('.smf-node-edit-start').click((event) => {
				event.stopPropagation();
				const colorInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-color`);
				const nameInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-name`);
				nameInput.prop("disabled", false);
				colorInput.prop("disabled", false);
				nameInput.focus();
				//nameInput.parent().addClass('smf-edit-active');
				$(`#smf-tree`).addClass('smf-edit-active');
				$(`#smf-save-actions-${node.id}`).css('display', 'flex');
			});
			li.find(`.smf-node-save-cancel`).click((event) => {
				event.stopPropagation();
				const colorInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-color`);
				const nameInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-name`);
				$(`#smf-save-actions-${node.id}`).hide();
				$(`#smf-edit-actions-${node.id}`).css('display', '');
				nameInput.prop("disabled", true);
				colorInput.prop("disabled", true);
				nameInput.val(node.name);
				colorInput.val(node.color);
				//nameInput.parent().removeClass('smf-edit-active');
				$(`#smf-tree`).removeClass('smf-edit-active');
			});
			li.find(`.smf-node-save-save`).click((event) => {
				event.stopPropagation();
				$(`#smf-node-save-save-${node.id} .spinner`).addClass('smf-active');
				$(`#smf-node-save-save-${node.id} .smf-node-icon `).css('opacity', '0%');
				const colorInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-color`);
				const nameInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-name`);
				const data = {
					action: 'simply_media_folders_update',
					id: node.id,
					name: nameInput.val(),
					color: colorInput.val(),
				};
				simplyMediaFoldersConfig.ajaxRequest(data, () => {
					const colorInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-color`);
					const nameInput = $(`.smf-node-edit[data-node-id=${node.id}] .smf-edit-name`);
					$(`#smf-save-actions-${node.id}`).hide();
					$(`#smf-edit-actions-${node.id}`).css('display', '');
					nameInput.prop("disabled", true);
					colorInput.prop("disabled", true);
					node.name = nameInput.val();
					node.color = colorInput.val();
					//nameInput.parent().removeClass('smf-edit-active');
					$(`#smf-tree`).removeClass('smf-edit-active');
					$(`#smf-node-save-save-${node.id} .spinner`).removeClass('smf-active');
					$(`#smf-node-save-save-${node.id} .smf-node-icon`).css('opacity', '100%');
				});
			});
			li.droppable({
				greedy: true,
				drop: (event, ui) => {
					event.stopPropagation();
					if ($(event.target).data('node-id')) {
						var folder_id = $(event.target).data("node-id"), media_id = null;
						if (simplyMediaFoldersConfig.library_mode === "list") {
							media_id = ui.draggable.id.replace('post-', '');
						} else {
							media_id = ui.draggable.data("id");
						}
						simplyMediaFoldersConfig.ajaxRequest(
							{ media_id, folder_id, action: 'simply_media_folders_attach' },
							() => simplyMediaFoldersConfig.reloadItems()
						);
					}
				}
			});
		}

		const mediaFoldersWrap = $(`<div class="smf-wrap">${htmlTemplate}</div>`);
		mediaFoldersWrap.append($('.wrap'));
		if($('#wpbody-content > div.notice').length > 0) {
			console.log($('wpbody-content > div.notice'));
            mediaFoldersWrap.insertAfter($('#wpbody-content > div.notice'));
		} else {
			mediaFoldersWrap.insertAfter($('#screen-meta-links'));
		}
		simplyMediaFoldersConfig.buildMediaFoldersTree(folderTemplate, onCreateLi, true);

		$('#smf-tree').on(
			'tree.move',
			function (event) {
				event.stopPropagation();
				const data = {
					action: 'simply_media_folders_update',
					id: event.move_info.moved_node.id,
				};
				if (event.move_info.position === 'inside') {
					data.parent_id = event.move_info.target_node.id;
				} else {
					data.parent_id = event.move_info.target_node.parent_id;
				}
				simplyMediaFoldersConfig.ajaxRequest(data, () => $('#smf-tree').tree('reload'));
			}
		);

		$('#smf-tree-wrap').droppable({
			greedy: true,
			drop: (event, ui) => {
				event.stopPropagation();
				var media_id = null;
				if (simplyMediaFoldersConfig.library_mode === "list") {
					media_id = ui.draggable.id.replace('post-', '');
				} else {
					media_id = ui.draggable.data("id");
				}
				const data = {
					action: 'simply_media_folders_attach',
					media_id: media_id,
					folder_id: null
				};
				simplyMediaFoldersConfig.ajaxRequest(data, () => simplyMediaFoldersConfig.reloadItems());
			}
		});


		// Bind the add header button.
		$('#smf-add').click(function () {
			$('#smf-add-spinner').addClass('smf-active');
			$('#smf-add-icon').css('opacity', '0%');
			var data = {
				name: jQuery('#smf-create-name').val(),
				color: jQuery('#smf-create-color').val()
			};
			if (simplyMediaFoldersConfig.selected) {
				data.parent_id = simplyMediaFoldersConfig.selected;
			} else {
				delete data.parent_id;
			}
			data.action = 'simply_media_folders_create';
			simplyMediaFoldersConfig.ajaxRequest(data, function (id) {
				data.id = id;
				if (data.parent_id !== null) {
					const other_node = $('#smf-tree').tree('getNodeById', data.parent_id)
					$('#smf-tree').tree('appendNode', data, other_node);
				} else {
					$('#smf-tree').tree('appendNode', data);
				}
				$('#smf-create-name').val('');
				$('#smf-create-color').val(simplyMediaFoldersConfig.default_color);
				$('#smf-add-icon').css('opacity', '100%');
				$('#smf-add-spinner').removeClass('smf-active');
				$('#smf-tree').tree('selectNode', $('smf-tree').tree('getNodeById', data.id));
			});
		});

		// Settings dialog activate button.
		$('#smf-settings').click(() => {
			$('#smf-settings-dialog').toggleClass('hidden');
			const btn = $('#smf-settings');
			btn.toggleClass('smf-dialog-showing');
			btn.html() === '✗' ? btn.html('⋮') : btn.html('✗');
		});

		// Prevent clicks from propagating beneath the dialog when it is open.
		$('#smf-settings-dialog').click((event) => event.stopPropagation())

		// Bind the Filter Enabled setting in the settings dialog.
		$('#smf-filter-enabled').change((res) => {
			$(`#smf-enable-filter-spinner`).addClass('smf-active');
			const data = {
				action: 'simply_media_folders_options',
				'filter_enabled': $(res.target).prop('checked') ?? 'false'
			};
			simplyMediaFoldersConfig.ajaxRequest(data, (res) => {
				simplyMediaFoldersConfig.reloadItems();
				$(`#smf-enable-filter-spinner`).removeClass('smf-active');
				simplyMediaFoldersConfig.filter_enabled = !simplyMediaFoldersConfig.filter_enabled;
			});
		});

		// Bind the Auto Open setting in the settings dialog.
		$('#smf-auto-open').change((res) => {
			$(`#smf-auto-open-spinner`).addClass('smf-active');
			const data = {
				action: 'simply_media_folders_options',
				'auto_open': $(res.target).val() ?? 0
			};
			simplyMediaFoldersConfig.ajaxRequest(data, (res) => {
				simplyMediaFoldersConfig.reloadItems();
				$(`#smf-auto-open-spinner`).removeClass('smf-active');
				simplyMediaFoldersConfig.auto_open = $(res.target).val();
			});
		});

		// Bind the Default Color setting in the settings dialog.
		$('#smf-default-color').change((res) => {
			$(`#smf-default-color-spinner`).addClass('smf-active');
			const data = {
				action: 'simply_media_folders_options',
				'default_color': $(res.target).val()
			};
			simplyMediaFoldersConfig.ajaxRequest(data, () => {
				simplyMediaFoldersConfig.default_color = $(res.target).val();
				$(`#smf-default-color-spinner`).removeClass('smf-active');
				$('#smf-create-color').val(simplyMediaFoldersConfig.default_color);
			});
		});

		// Make media library images in grid mode draggable.
		if (wp.media !== undefined && wp.media.view !== undefined) {
			var originalLibrary = wp.media.view.Attachment.Library;
			wp.media.view.Attachment.Library = wp.media.view.Attachment.Library.extend({
				initialize: function () {
					originalLibrary.prototype.initialize.apply(this, arguments);

					this.on("ready", function () {
						this.$el.draggable({
							zIndex: 2500,
							helper: "clone",
							appendTo: "body",
							cursorAt: { top: 50, left: 50 }
						});
					});
				},
			});

			if (wp && typeof (wp.Uploader) == "function") {
				$.extend(wp.Uploader.prototype, {
					init: function () {

						// Set the folder_id parameter as expected by PHP, and show toast notification.
						this.uploader.bind("BeforeUpload", function (uploader, file) {
							if (simplyMediaFoldersConfig.selected) {
								uploader.settings.multipart_params['folder_id'] = simplyMediaFoldersConfig.selected;
							}
						});
						this.uploader.bind("UploadComplete", function () {
							simplyMediaFoldersConfig.reloadItems();
						});
					}
				})
			}
		}

		// Make media library images in list mode draggable.
		if (simplyMediaFoldersConfig.library_mode === "list") {
			$("#wpbody-content .wp-list-table tbody tr:not(.no-items)").draggable({
				zIndex: 2500,
				helper: "clone",
				appendTo: "body",
				cursorAt: { top: 25, left: 25 }
			});
		}
	});
})(jQuery);
