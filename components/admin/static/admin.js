var Admin = {

    confirmRedirect: function(message, url) {
        if (confirm(message)) {
            location.href = url;
        }
    }

};
