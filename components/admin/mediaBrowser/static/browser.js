var MediaBrowser = {
  
    folders: [],
    files: [],
    selectedFile: null,
    openedFolders: [],
    selectedFolder: null,
    
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
        this.locale = options.locale || 'en';
        this.foldersRequestUrl = options.foldersRequestUrl || '';
        this.filesRequestUrl = options.filesRequestUrl || '';
        this.thumbnailRequestUrl = options.thumbnailRequestUrl || '';
        this.newFolderRequestUrl = options.newFolderRequestUrl || '';
        this.renameFolderRequestUrl = options.renameFolderRequestUrl || '';
        this.deleteFolderRequestUrl = options.deleteFolderRequestUrl || '';
        this.folderAddButton.addEventListener('click', this.newFolder.bind(this));
        this.folderRenameButton.addEventListener('click', this.renameFolder.bind(this));
        this.folderDeleteButton.addEventListener('click', this.deleteFolder.bind(this));
    },
    
    ajaxRequest: function(options) {
        var xhr = new XMLHttpRequest();
        var method = options.method || 'post';
        var async = options.async || true;
        var data = options.data || {};
        var url = options.url;
        data.locale = this.locale;
        method = method.toUpperCase();
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
        var fileClassName = this.selectedFile === null ? 'disabled' : '';
        if (this.selectedFolder !== null) {
            folderAddUploadClassName = '';
            if (this.selectedFolder.parent_id) {
                folderDeleteRenameClassName = '';
            }
        }
        this.fileUseButton.style.display = this.selectedFile === null ? 'none' : 'block';
        this.folderAddButton.className = folderAddUploadClassName;
        this.folderDeleteButton.className = folderDeleteRenameClassName;
        this.folderRenameButton.className = folderDeleteRenameClassName;
        this.fileUploadButton.className = folderAddUploadClassName;
        this.fileDeleteButton.className = fileClassName;
        this.fileRenameButton.className = fileClassName;
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
        var oldFolder = this.findFolderById(folder.id, this.folders);
        if (oldFolder !== null) {
            return this.refreshFolder(oldFolder, folder);
        }
        // init folder's children
        folder.folders = [];
        // find parent
        var parentFolders = this.folders;
        var parent = this.findFolderById(folder.parent_id, this.folders);
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
                var found = this.findFolderById(folderId, f.folders);
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
            var index = this.openedFolders.indexOf(f.id);
            f.level = level;
            allFolders.push(f);
            if (f.folders.length && index !== -1) {
                this.findAllFoldersForRender(allFolders, f.folders, level + 1);
            }
        }
    },
    
    requestFolders: function(id) {
        this.ajaxRequest({
            url: this.foldersRequestUrl + '/' + id,
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
        this.ajaxRequest({
            url: this.filesRequestUrl + '/' + id,
            success: function(xhr) {
                MediaBrowser.selectedFile = null;
                MediaBrowser.files = JSON.parse(xhr.responseText);
                MediaBrowser.renderFiles();
                MediaBrowser.adjustButtons();
            }            
        });
    },

    getSelectedFolderId: function() {
        if (this.selectedFolder !== null) {
            return this.selectedFolder.id;
        }
        return 0;
    },

    getSelectedFileId: function() {
        if (this.selectedFile !== null) {
            return this.selectedFile.id;
        }
        return 0;
    },
    
    clickFolder: function(id) {
        var index = this.openedFolders.indexOf(id);
        var opened = index !== -1;
        var selected = id === this.getSelectedFolderId();
        if (!selected) {
            this.selectedFolder = this.findFolderById(id, this.folders);
            this.selectedFile = null;
            this.adjustButtons();
            this.requestFiles(id);
        }        
        if (selected && opened) {
            this.openedFolders.splice(index, 1);
            this.renderFolders();
        } else if (!opened) {
            this.openedFolders.push(id);
            this.requestFolders(id);
        } else {
            this.renderFolders();
        }        
    },

    findFileById: function(id) {
        for (var i = 0; i < this.files.length; i++) {
            var f = this.files[i];
            if (f.id === id) {
                return f;
            }
        }
        return null;
    },
    
    clickFile: function(id) {
        var elem;
        if (this.selectedFile !== null) {
            elem = document.querySelector('a[data-id="' + this.selectedFile.id + '"]');
            elem.className = 'item';
        }
        this.selectedFile = this.findFileById(id);
        elem = document.querySelector('a[data-id="' + id + '"]');
        elem.className = 'item selected';
        this.adjustButtons();
    },
    
    newFolder: function(event, defaultName) {
        if (this.selectedFolder === null) {
            return alert('Please select a folder!');
        }
        var parentId = this.selectedFolder.id;
        var name = prompt('New name', defaultName || '');
        if (name === null) {
            return;
        }
        this.ajaxRequest({
            url: this.newFolderRequestUrl,
            data: {
                'name': name,
                'parent_id': parentId
            },
            success: function(xhr) {
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

    getSelectedFolderForRenameOrDelete: function() {
        var folder = this.selectedFolder;
        if (folder === null) {
            return alert('Please select a folder!');
        }
        if (!folder.parent_id) {
            return;
        }
        return folder;
    },
    
    renameFolder: function(event) {
        var folder = this.getSelectedFolderForRenameOrDelete();
        if (!folder) {
            return;
        }
        var name = prompt('Rename', folder.name);
        if (name === null || name === folder.name) {
            return;
        }
        this.ajaxRequest({
            url: this.renameFolderRequestUrl,
            data: {
                'name': name,
                'parent_id': folder.parent_id,
                'id': folder.id
            },
            success: function(xhr) {
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

    deleteFolder: function(event) {
        var folder = this.getSelectedFolderForRenameOrDelete();
        if (!folder) {
            return;
        }
        this.ajaxRequest({
            url: this.deleteFolderRequestUrl,
            data: {'id': folder.id},
            success: function(xhr) {
                var data = JSON.parse(xhr.responseText);
                var error = data.error || '';
                if (error) {
                    alert(error);
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
        var html = '<ul>';
        var elem = document.getElementById('folders');
        this.findAllFoldersForRender(allFolders, this.folders, 0);
        for (var i = 0; i < allFolders.length; i++) {
            html += this.renderFolder(allFolders[i]);
        }
        html += '</ul>';
        elem.innerHTML = html;
    },

    renderFolder: function(f) {
        var index = this.openedFolders.indexOf(f.id);
        var icon = index === -1 ? '' : '-open';
        var selected = f.id === this.getSelectedFolderId();
        var html = '<li' + (selected ? ' class="selected"' : '') + '>';
        html += '<a style="padding-left: ' + (f.level * 24) + 'px"';
        html += ' href="javascript:MediaBrowser.clickFolder(' + f.id + ')">';
        html += '<i class="fa fa-folder' + icon + '"></i>';
        html += '<span>' + this.escapeString(f.name) + '</span>';
        html += '</a>';
        html += '</li>';
        return html;
    },
    
    renderFiles: function() {
        var html = '';
        var elem = document.getElementById('files');
        for (var i = 0; i < this.files.length; i++) {
            html += this.renderFile(this.files[i]);
        }
        elem.innerHTML = html;        
    },
    
    renderFile: function(f) {
        var html = '';
        var name = f.name;
        var icon = 'fa-file';
        var ext = '';
        var selected = f.id === this.getSelectedFileId();
        var imgSrc = this.thumbnailRequestUrl + '/' + f.id;
        if (f.extension) {
            ext = f.extension.toLowerCase();
            name += '.' + f.extension;
        }
        if (this.iconByExtension.hasOwnProperty(ext)) {
            icon += '-' + this.iconByExtension[ext];
        }
        var isImage = this.imageExtensions.indexOf(ext) !== -1;
        html += '<a href="javascript:MediaBrowser.clickFile(' + f.id + ')" class="item';
        if (selected) {
            html += ' selected';
        }
        html += '" data-id="' + f.id + '">';
        if (isImage) {
            html += '<span><img src="' + imgSrc + '"></span>';
        } else {
            html += '<i class="fa ' + icon + '"></i>';
        }
        html += name;
        html += '</a>';
        return html;
    }
    
};
