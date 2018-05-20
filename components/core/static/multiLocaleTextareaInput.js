var MultiLocaleTextareaInput = {

    allLocale: [],
    
    tabClick: function() {
        var name = this.getAttribute('data-name');
        var locale = this.getAttribute('data-locale');
        var tabs = document.querySelectorAll('.multi-locale-textarea-input-tab[data-name="' + name + '"]');
        var containers = document.querySelectorAll('.multi-locale-textarea-input-container[data-name="' + name + '"]');
        var i, node, nodeLocale;
        for (i = 0; i < tabs.length; i++) {
            node = tabs[i];
            nodeLocale = node.getAttribute('data-locale');
            if (nodeLocale === locale) {
                node.classList.add('multi-locale-textarea-input-tab-active');
            } else {
                node.classList.remove('multi-locale-textarea-input-tab-active');
            }
        }
        for (i = 0; i < containers.length; i++) {
            node = containers[i];
            nodeLocale = node.getAttribute('data-locale');
            node.style.display = nodeLocale === locale ? 'block': 'none';
        }
    },
    
    init: function(allLocale) {
        var tabs = document.querySelectorAll('.multi-locale-textarea-input-tab');
        var containers = document.querySelectorAll('.multi-locale-textarea-input-container');
        var i, node;
        this.allLocale = allLocale;
        for (i = 0; i < tabs.length; i++) {
            node = tabs[i];
            node.addEventListener('click', this.tabClick);
        }
        for (i = 0; i < containers.length; i++) {
            node = containers[i];
            node.style.display = i === 0 ? 'block' : 'none';            
        }
    }

};

