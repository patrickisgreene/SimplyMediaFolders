(function ($) {
	'use strict';

	simplyMediaFoldersConfig.ajaxRequest = (data, success) => {
		data.nonce = simplyMediaFoldersConfig.nonce;
		$.ajax({ url: ajaxurl, method: 'POST', data, success, cache: false });
	};

	simplyMediaFoldersConfig.reloadItems = () => {
		const params = new URLSearchParams(window.location.search);

		if (simplyMediaFoldersConfig.library_mode === 'list') {
			const selectedNode = simplyMediaFoldersConfig.selected;
			if (simplyMediaFoldersConfig.selected) {
				$('#posts-filter').append(`<input name="folder_id" value="${selectedNode}" class="hidden" />`);
			}
			$("#posts-filter").submit();
			$('#posts-filter input[name="folder_id"]').remove();
		} else {
			simplyMediaFoldersConfig.reloadGridItems();
		}
	}

	simplyMediaFoldersConfig.reloadGridItems = () => {
		const folderId = simplyMediaFoldersConfig.selected;
		console.log(wp.media.frame);
		const content = wp.media.frame.content.get();
		if (content !== null && Object.hasOwn(content, 'collection')) {
			wp.media.frame.content.get().collection.props.set({ ignore: (+ new Date()), 'folder_id': folderId });
			wp.media.frame.content.get().options.selection.reset();
		} else {
			wp.media.frame.library.props.set({ ignore: (+ new Date()) });
		}
	}

	simplyMediaFoldersConfig.buildMediaFoldersTree = (folderTemplate, listItem) => {
		$('#smf-tree').tree({
			autoOpen: simplyMediaFoldersConfig.auto_open == 0 ? false : simplyMediaFoldersConfig.auto_open - 1,
			dragAndDrop: listItem ? true : false,
			useContextMenu: false,
			dataUrl: {
				method: 'POST',
				url: ajaxurl,
				data: {
					action: 'simply_media_folders_list'
				},
				cache: false,
				success: (res) => {
					$('#smf-tree').tree('loadData', res.data ?? []);
					if (simplyMediaFoldersConfig.selected) {
						const $tree = $('#smf-tree');
						const node = $tree.tree('getNodeById', simplyMediaFoldersConfig.selected);
						$tree.tree('selectNode', node);
					}
				}
			},
			onCreateLi: (node, li) => {
				li.attr("data-node-id", node.id);
				li.find('.jqtree-element').append($(folderTemplate(node)));
				if(listItem) {
					listItem(node, li);
				}
			},
			onLoadFailed: (res) => console.log(res)
		});

		$('#smf-tree').click((event) => event.stopPropagation());
		$('#smf-header').click((event) => event.stopPropagation());

		$('#smf-tree').on(
			'tree.select',
			function (event) {
				if (event.node) {
					if (simplyMediaFoldersConfig.selected !== event.node.id) {
						simplyMediaFoldersConfig.selected = event.node.id;
						simplyMediaFoldersConfig.reloadItems();
					}
				} else {
					if (simplyMediaFoldersConfig.selected !== null) {
						simplyMediaFoldersConfig.selected = null;
						simplyMediaFoldersConfig.reloadItems();
					}
				}
			}
		);

		$('#smf-tree-wrap').click(() => {
			if (simplyMediaFoldersConfig.selected !== null) {
				simplyMediaFoldersConfig.selected = null;
				$('#smf-tree').tree('selectNode', null);
				simplyMediaFoldersConfig.reloadItems();
			}
		});

	}

})(jQuery);
