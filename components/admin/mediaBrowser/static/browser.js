var MediaBrowser = {
  
    folders: [],
    files: [],
    selectedFile: -1,
    openedFolders: [],
    selectedFolder: -1,
    
    locale: 'en',
    
    folderDeleteButton: document.getElementById('folder_delete_button'),
    folderRenameButton: document.getElementById('folder_rename_button'),
    folderAddButton: document.getElementById('folder_add_button'),
    fileDeleteButton: document.getElementById('file_delete_button'),
    fileRenameButton: document.getElementById('file_rename_button'),
    fileUploadButton: document.getElementById('file_upload_button'),    
    fileUseButton: document.getElementById('file_use_button'),
    
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
    
    imageExtensions: ['jpg', 'jpeg', 'png', 'gif'],
    
    init: function(options) {
        MediaBrowser.locale = options.locale || 'en';
        MediaBrowser.foldersRequestUrl = options.foldersRequestUrl || '';
        MediaBrowser.filesRequestUrl = options.filesRequestUrl || '';
        MediaBrowser.thumbnailRequestUrl = options.thumbnailRequestUrl || '';
        MediaBrowser.newFolderRequestUrl = options.newFolderRequestUrl || '';
        MediaBrowser.renameFolderRequestUrl = options.renameFolderRequestUrl || '';
        MediaBrowser.folderAddButton.addEventListener('click', MediaBrowser.newFolder);
        MediaBrowser.folderRenameButton.addEventListener('click', MediaBrowser.renameFolder);
    },
    
    ajaxRequest: function(options) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState !== 4) {
                return;
            }
            if (this.status === 200) {
                options.success(xhr);
            } else {
                alert('Request failed (Status: ' + this.status + ')');
            }
        };
        var method = options.method || 'post';
        var async = options.async || true;
        var data = options.data || {};        
        var url = options.url;
        data['locale'] = MediaBrowser.locale;
        method = method.toUpperCase();
        xhr.open(method, url, async);
        xhr.overrideMimeType('application/json');
        xhr.send(JSON.stringify(data));
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
    
    adjustButtons: function() {
        var folderAddUploadClassName = 'disabled';
        var folderDeleteRenameClassName = 'disabled';
        var fileClassName = MediaBrowser.selectedFile === -1 ? 'disabled' : '';
        if (MediaBrowser.selectedFolder !== -1) {
            folderAddUploadClassName = '';
            var folder = MediaBrowser.findFolderById(MediaBrowser.selectedFolder, MediaBrowser.folders);
            if (folder.parent_id) {
                folderDeleteRenameClassName = '';
            }
        }
        MediaBrowser.fileUseButton.style.display = MediaBrowser.selectedFile === -1 ? 'none' : 'block';
        MediaBrowser.folderAddButton.className = folderAddUploadClassName;
        MediaBrowser.folderDeleteButton.className = folderDeleteRenameClassName;
        MediaBrowser.folderRenameButton.className = folderDeleteRenameClassName;
        MediaBrowser.fileUploadButton.className = folderAddUploadClassName;
        MediaBrowser.fileDeleteButton.className = fileClassName;
        MediaBrowser.fileRenameButton.className = fileClassName;
    },
    
    refreshFolder: function(oldFolder, folder) {
        for (var prop in folder) {
            if (folder.hasOwnProperty(prop)) {
                oldFolder[prop] = folder[prop];
            }
        }        
    },
    
    pushFolder: function(folder) {        
        // check for existing folder
        var oldFolder = MediaBrowser.findFolderById(folder.id, MediaBrowser.folders);
        if (oldFolder !== null) {
            return MediaBrowser.refreshFolder(oldFolder, folder);
        }
        // init folder's children
        folder.folders = [];
        // find parent
        var parentFolders = MediaBrowser.folders;
        var parent = MediaBrowser.findFolderById(folder.parent_id, MediaBrowser.folders);
        if (parent !== null) {
            parentFolders = parent.folders;
        }        
        // add the folder to the parent's folder list while keeping alphabetic order
        for (var i = 0; i < parentFolders.length; i++) {
            var f = parentFolders[i];
            if (folder.name < f.name) {
                parentFolders.splice(i, 0, folder);
                return;
            }
        }
        parentFolders.push(folder);
    },
    
    findFolderById: function(folderId, folders) {
        for (var i = 0; i < folders.length; i++) {            
            var f = folders[i];
            if (f.id === folderId) {
                return f;
            }
            if (f.folders.length) {
                var found = MediaBrowser.findFolderById(folderId, f.folders);
                if (found) {
                    return found;
                }
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
    
    requestFolders: function(id) {
        MediaBrowser.ajaxRequest({
            url: MediaBrowser.foldersRequestUrl + '/' + id,
            success: function(xhr) {
                var folders = JSON.parse(xhr.responseText);
                for (var i = 0; i < folders.length; i++) {
                    MediaBrowser.pushFolder(folders[i]);
                }
                MediaBrowser.renderFolders();
                MediaBrowser.adjustButtons();
            }
        });
    },
    
    requestFiles: function(id) {
        MediaBrowser.ajaxRequest({
            url: MediaBrowser.filesRequestUrl + '/' + id,
            success: function(xhr) {
                MediaBrowser.selectedFile = -1;
                MediaBrowser.files = JSON.parse(xhr.responseText);
                MediaBrowser.renderFiles();
                MediaBrowser.adjustButtons();
            }            
        });
    },
    
    clickFolder: function(id) {
        var index = MediaBrowser.openedFolders.indexOf(id);
        var opened = index !== -1;
        var selected = id === MediaBrowser.selectedFolder;
        if (!selected) {
            MediaBrowser.selectedFolder = id;
            MediaBrowser.adjustButtons();
            MediaBrowser.requestFiles(id);
        }        
        if (selected && opened) {
            MediaBrowser.openedFolders.splice(index, 1);
            MediaBrowser.renderFolders();
        } else if (!opened) {
            MediaBrowser.openedFolders.push(id);
            MediaBrowser.requestFolders(id);
        } else {
            MediaBrowser.renderFolders();
        }        
    },
    
    clickFile: function(id) {
        var elem;
        if (MediaBrowser.selectedFile !== -1) {
            elem = document.querySelector('a[data-id="' + MediaBrowser.selectedFile + '"]');
            elem.className = 'item';
        }
        MediaBrowser.selectedFile = id;
        elem = document.querySelector('a[data-id="' + id + '"]');
        elem.className = 'item selected';
        MediaBrowser.adjustButtons();
    },
    
    newFolder: function(event, defaultName) {
        if (MediaBrowser.selectedFolder === -1) {
            return alert('Please select a folder!');
        }
        var parentId = MediaBrowser.selectedFolder;
        var name = prompt('New name', defaultName || '');
        if (name === null) {
            return;
        }
        MediaBrowser.ajaxRequest({
            'url': MediaBrowser.newFolderRequestUrl,
            'data': {
                'name': name,
                'parent_id': parentId
            },
            'success': function(xhr) {
                var data = JSON.parse(xhr.responseText);
                var error = data.error || '';
                if (error) {
                    alert(error);
                    MediaBrowser.newFolder(event, name);
                } else {
                    MediaBrowser.requestFolders(parentId);
                }                    
            }
        });
    },
    
    renameFolder: function(event) {
        if (MediaBrowser.selectedFolder === -1) {
            return alert('Please select a folder!');
        }
        var id = MediaBrowser.selectedFolder;
        var folder = MediaBrowser.findFolderById(id, MediaBrowser.folders);
        var name = prompt('Rename', folder.name);
        if (name === null || name === folder.name) {
            return;
        }
        MediaBrowser.ajaxRequest({
            'url': MediaBrowser.renameFolderRequestUrl,
            'data': {
                'name': name,
                'parent_id': folder.parent_id,
                'id': id
            },
            'success': function(xhr) {
                var data = JSON.parse(xhr.responseText);
                var error = data.error || '';
                if (error) {
                    alert(error);
                    MediaBrowser.renameFolder(event);
                } else {
                    MediaBrowser.requestFolders(folder.parent_id);
                }                    
            }
        });        
    },
    
    goToFile: function(id) {
        
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
            html += MediaBrowser.renderFile(MediaBrowser.files[i]);
        }
        var elem = document.getElementById('files');
        elem.innerHTML = html;        
    },
    
    renderFile: function(f) {
        var html = '';
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
        var isImage = MediaBrowser.imageExtensions.indexOf(ext) !== -1;
        html += '<a href="javascript:MediaBrowser.clickFile(' + f.id + ')" class="item" data-id="' + f.id + '">';
        if (isImage) {
            var src = MediaBrowser.thumbnailRequestUrl + '/' + f.id;
            html += '<span><img src="' + src + '"></span>';
        } else {
            html += '<i class="fa ' + icon + '"></i>';
        }
        html += name;
        html += '</a>';
        return html;
    }
    
};
