var MediaBrowser = {
  
    folders: [],
    files: [],
    selectedFile: -1,
    openedFolders: [],
    selectedFolder: -1,    
    
    iconByExtension: {
        'doc': 'word',
        'docx': 'word',
        'odt': 'word',
        'rtf': 'word',
        'ppt': 'powerpoint',
        'pptx': 'powerpoint',
        'odp': 'powerpoint',
        'xls': 'excel',
        'xlsx': 'excel',
        'ods': 'excel',
        'pdf': 'pdf',
        'wav': 'audio',
        'mp3': 'audio',
        'ogg': 'audio',
        'flac': 'audio',
        'zip': 'archive',
        'rar': 'archive',
        '7z': 'archive',
        'tar': 'archive',
        'gz': 'archive',
        'tgz': 'archive',
        'html': 'code',
        'js': 'code',
        'css': 'code',
        'jpg': 'image',
        'jpeg': 'image',
        'png': 'image',
        'bmp': 'image',
        'gif': 'image'
    },
    
    init: function(options) {
        MediaBrowser.foldersRequestUrl = options.foldersRequestUrl || '';
        MediaBrowser.filesRequestUrl = options.filesRequestUrl || '';
        MediaBrowser.thumbnailRequestUrl = options.thumbnailRequestUrl || '';
    },
    
    ajaxRequest: function(options) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                options.success(xhr);
            }
            if ((this.readyState === 3 || this.readyState === 4) && this.status !== 200) {
                alert('Request failed (Status: ' + this.status + ')');
            }
        };
        var method = options.method || 'get';
        var async = options.async || true;
        var data = options.data || {};
        var url = options.url;
        xhr.open(method.toUpperCase(), url, async);
        xhr.send(data);
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
    
    refreshFolder: function(oldFolder, folder) {
        for (var prop in folder) {
            if (folder.hasOwnProperty(prop)) {
                oldFolder[prop] = folder[prop];
            }
        }        
    },
    
    addFolder: function(folder) {
        var folders = MediaBrowser.folders;
        var oldFolder = MediaBrowser.findFolderById(folder.id, folders);
        if (oldFolder !== null) {
            return MediaBrowser.refreshFolder(oldFolder, folder);
        }
        var parent = MediaBrowser.findFolderById(folder.parent_id, folders);
        if (parent !== null) {
            folders = parent.folders;
        }
        folder.folders = [];
        folders.push(folder);
    },
    
    findFolderById: function(folderId, folders) {
        for (var i = 0; i < folders.length; i++) {
            var f = folders[i];
            if (f.id === folderId) {
                return f;
            }
            if (f.folders.length) {
                return MediaBrowser.findFolderById(folderId, f.folders);
            }
        }
        return null;
    },
    
    findAllFoldersForRender: function(allFolders, folders, level) {
        for (var i = 0; i < folders.length; i++) {
            var f = folders[i];
            var index = MediaBrowser.openedFolders.indexOf(f.id);
            f.level = level;
            allFolders.push(f);
            if (f.folders.length && index !== -1) {
                MediaBrowser.findAllFoldersForRender(allFolders, f.folders, level + 1);
            }
        }
    },
    
    requireFolders: function(id, callback) {
        MediaBrowser.ajaxRequest({
            url: MediaBrowser.foldersRequestUrl + '/' + id,
            method: 'post',
            success: function(xhr) {
                var folders = JSON.parse(xhr.responseText);
                for (var i = 0; i < folders.length; i++) {
                    MediaBrowser.addFolder(folders[i]);
                }
                callback();
            }
        });
    },
    
    requireFiles: function(id) {
        MediaBrowser.ajaxRequest({
            url: MediaBrowser.filesRequestUrl + '/' + id,
            success: function(xhr) {
                MediaBrowser.files = JSON.parse(xhr.responseText);
                MediaBrowser.renderFiles();
            }            
        });
    },
    
    clickFolder: function(id) {
        var index = MediaBrowser.openedFolders.indexOf(id);
        var opened = index !== -1;
        var selected = id === MediaBrowser.selectedFolder;
        if (!selected) {
            MediaBrowser.selectedFolder = id;
            MediaBrowser.requireFiles(id);
        }        
        if (selected && opened) {
            MediaBrowser.openedFolders.splice(index, 1);
            MediaBrowser.renderFolders();
        } else if (!opened) {
            MediaBrowser.openedFolders.push(id);
            MediaBrowser.requireFolders(id, MediaBrowser.renderFolders);
        } else {
            MediaBrowser.renderFolders();
        }        
    },
    
    goToFolder: function(id) {
        
    },
    
    renderFolders: function() {
        var allFolders = [];
        MediaBrowser.findAllFoldersForRender(allFolders, MediaBrowser.folders, 0);
        var html = '<ul>';
        for (var i = 0; i < allFolders.length; i++) {
            var f = allFolders[i];
            var index = MediaBrowser.openedFolders.indexOf(f.id);
            var selected = MediaBrowser.selectedFolder === f.id;
            var icon = index === -1 ? '' : '-open';
            html += '<li' + (selected ? ' class="selected"' : '') + '>';
            html += '<a style="padding-left: ' + (f.level * 24) + 'px"';
            html += ' href="javascript:MediaBrowser.clickFolder(' + f.id + ')">';
            html += '<i class="fa fa-folder' + icon + '"></i>';
            html += '<span>' + MediaBrowser.escapeString(f.name) + '</span>';
            html += '</a>';
            html += '</li>';
        }
        html += '</ul>';
        var elem = document.getElementById('folders');
        elem.innerHTML = html;
    },
    
    renderFiles: function() {
        var html = '';
        for (var i = 0; i < MediaBrowser.files.length; i++) {
            var f = MediaBrowser.files[i];
            var name = f.name;
            var icon = 'fa-file';
            var ext = '';
            if (f.extension) {
                ext = f.extension.toLowerCase();
                name += '.' + f.extension;
                if (MediaBrowser.iconByExtension.hasOwnProperty(ext)) {
                    icon += '-' + MediaBrowser.iconByExtension[ext];
                }
            }            
            html += '<a href="#" class="item">';
            if (ext === 'jpeg' || ext === 'jpg' || ext === 'png' || ext === 'gif') {
                html += '<span><img src="' + MediaBrowser.thumbnailRequestUrl + '/' + f.id+ '"></span>';
            } else {
                html += '<i class="fa ' + icon + '"></i>';
            }
            html += name;
            html += '</a>';
        }
        var elem = document.getElementById('files');
        elem.innerHTML = html;        
    }
    
};
