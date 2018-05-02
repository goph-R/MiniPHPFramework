var MediaBrowser = {
  
    folders: [],
    files: [],
    
    init: function(options) {
        MediaBrowser.foldersRequestUrl = options.foldersRequestUrl || '';
    },
    
    ajaxRequest: function(options) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                options.success(xhr);
            }
            if ((this.readyState === 3 || this.readyState === 4) && this.status !== 200) {
                options.fail(xhr);
            }
        };
        xhr.open(options.method || 'GET', options.url, options.async || true);
        xhr.send(options.data || {});
    },
            
    escapeString: function(str) {
        var tagsToReplace = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;'
        };
        return str.replace(/[&<>]/g, function(tag) {
            return tagsToReplace[tag] || tag;
        });
    },
    
    addFolder: function(folder) {
        folder.folders = [];
        var folders = MediaBrowser.folders;
        var parent = MediaBrowser.findParent(folder, folders);
        if (parent !== null) {
            folders = parent.folders;
        }
        folders.push(folder);
    },
    
    findParent: function(folder, folders) {
        for (var i = 0; i < folders.length; i++) {
            var f = folders[i];
            if (folder.parent_id === f.id) {
                return folders[i];
            }
            if (f.folders.length) {
                MediaBrowser.findParent(folder, f.folders);
            }
        }
        return null;
    },
    
    findAllFoldersAsFlat: function(allFolders, folders, level) {
        for (var i = 0; i < folders.length; i++) {
            var f = folders[i];
            f.level = level;
            allFolders.push(f);
            if (f.folders.length) {
                MediaBrowser.findAllFoldersAsFlat(allFolders, f.folders, level + 1);
            }
        }
    },
    
    requireFolders: function(id) {
        MediaBrowser.ajaxRequest({
            url: MediaBrowser.foldersRequestUrl + '/' + id,
            success: function(xhr) {
                var folders = JSON.parse(xhr.responseText);
                for (var i = 0; i < folders.length; i++) {
                    MediaBrowser.addFolder(folders[i]);
                }
                MediaBrowser.renderFolders();
            }
        });
    },
    
    renderFolders: function() {
        var allFolders = [];
        MediaBrowser.findAllFoldersAsFlat(allFolders, MediaBrowser.folders, 0);
        var html = '<ul>';
        for (var i = 0; i < allFolders.length; i++) {
            var f = allFolders[i];
            html += '<li><a style="padding-left: ' + (f.level * 24) + 'px" href="#">';
            html += '<i class="fa fa-folder-open"></i>';
            html += '<span>' + MediaBrowser.escapeString(f.name) + '</span>';
            html += '</a></li>';
        }
        html += '</ul>';
        var elem = document.getElementById('folders');
        elem.innerHTML = html;
    }
    
};
