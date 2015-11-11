function getItem(item_id) {

    var ajaxRequest;

    try {
        // Opera 8.0+, Firefox, Safari
        ajaxRequest = new XMLHttpRequest();
    } catch (e) {
        // Internet Explorer Browsers
        try {
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                // Something went wrong
                alert("Your browser broke!");
                return false;
            }
        }
    }

    ajaxRequest.onreadystatechange = function () {
        if (ajaxRequest.readyState === 4) {
            var ajaxBackground = document.getElementById('overlay');
            var ajaxDisplay = document.getElementById('ajaxDiv');
            ajaxDisplay.innerHTML = ajaxRequest.responseText;
            //Display the items
            ajaxBackground.style.display = 'block';
            ajaxDisplay.style.display = 'block';
            //Add code to close the div
            //Placed in function to avoid early firing
            ajaxDisplay.onclick = function () {
                hideItem();                
            };
             ajaxBackground.onclick = function () {
                hideItem();
            };
        }
    };

    ajaxRequest.open("GET", "scripts/getItem.php?item_id=" + item_id, true);
    ajaxRequest.send(null);
}

function hideItem() {
    var ajaxBackground = document.getElementById('overlay');
    var ajaxDisplay = document.getElementById('ajaxDiv');
    ajaxBackground.style.display = 'none';
    ajaxDisplay.style.display = 'none';
}