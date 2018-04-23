var MultiLocaleTextareaInput = {
    
    allLocale: [],
    
    tabClick: function() {
        var name = this.getAttribute('data-name');
        var locale = this.getAttribute('data-locale');
        var nodes = document.querySelectorAll('.multi-locale-textarea-input-container [data-name="' + name + '"]');
        for (var i = 0; i < this.allLocale; i++) {
            var node = nodes[i];
            var nodeLocale = node.getAttribute('data-locale');
            node.style.display = nodeLocale === locale ? 'block': 'none';
        }
    },
    
    init: function(allLocale) {
        this.allLocale = allLocale;
        var nodes = document.querySelectorAll('.multi-locale-textarea-input-tab');
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            node.addEventListener('click', this.tabClick);
        }
        var nodes = document.querySelectorAll('.multi-locale-textarea-input-container');
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            node.style.display = i === 0 ? 'block' : 'none';            
        }
    }

};

