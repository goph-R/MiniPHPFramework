var Confirm = {
    texts: {
        delete: 'Are you sure to delete?'
    },
    redirect: function(textId, url) {
        if (confirm(Confirm.texts[textId])) {
            location.href = url;
        }
    }
};