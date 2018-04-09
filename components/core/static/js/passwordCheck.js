var PasswordCheck = {
    
    texts: {
        low: 'Low',
        normal: 'Normal',
        high: 'High'
    },
    
    add: function(inputSelector) {
        var inputElement = document.querySelector(inputSelector);        
        var messageElement = inputElement.parentNode.insertBefore(
            document.createElement('span'),
            inputElement.nextSibling
        );
        var adjust = function() {
            level = 'low';
            s = inputElement.value;
            if (s.length > 8) {
                numNumber = s.length - s.replace(/[0-9]/g, '').length;
                numUpper = s.length - s.replace(/[A-Z]/g, '').length;
                if (numUpper > 0 && numNumber > 1) {
                    level = 'high';
                } else {
                    level = 'normal';
                }
            }            
            messageElement.className = 'password-check-text password-check-text-' + level;
            messageElement.innerHTML = PasswordCheck.texts[level];
        };
        adjust();
        inputElement.addEventListener('keyup', adjust);
    }
    
};

