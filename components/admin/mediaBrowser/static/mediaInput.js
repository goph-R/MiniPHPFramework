var MediaInput = {

    imageExtensions: ['jpg', 'jpeg', 'png', 'gif'],

    init: function(id, options) {
        var mediaBrowserUrl = options.mediaBrowserUrl || '';
        var span = document.getElementById(id + '_display');
        this.thumbnailUrl = options.thumbnailUrl || '';
        span.addEventListener('click', function() {
            var ps = mediaBrowserUrl.indexOf('?') === -1 ? '?' : '&';
            var url = mediaBrowserUrl + ps + 'input_id=' + id;
            window.open(url, 'MediaBrowserWindow', 'width=1024,height=600');
        });
    },

    setValue: function(id, file) {
        var input = document.getElementById(id);
        var span = document.getElementById(id + '_display');
        var isImage = false;
        var name = '-- Select --';
        var fileId = 0;
        if (file) {
            var ext = file.extension.toLowerCase();
            isImage = this.imageExtensions.indexOf(ext) !== -1;
            name = file.name;
            fileId = file.id;
            if (file.extension) {
                name += '.' + file.extension;
            }
        }
        input.setAttribute('value', fileId);
        if (isImage) {
            var imgSrc = this.thumbnailUrl.replace('{id}', file.id);
            imgSrc = imgSrc.replace('%7Bid%7D', file.id);
            span.style.backgroundImage = "url('" + imgSrc + "')";
            span.style.width = '90px';
            span.style.height = '90px';
            span.innerHTML = '';
        } else {
            span.style.backgroundImage = 'none';
            span.style.width = 'auto';
            span.style.height = 'auto';
            span.innerHTMl = name;
        }
    }

};