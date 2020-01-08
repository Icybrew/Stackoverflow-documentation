import '../css/style.scss';

import 'jquery/dist/jquery.slim.min.js';
import 'popper.js/dist/popper.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';

const axios = require('axios').default;

axios.defaults.baseURL = "http://localhost/Stackoverflow-documentation/public/";

/* Search category */
let category = document.querySelector('#search-category');

category.addEventListener('input', (e) => {
    let text = e.target.value;

    axios.get('api/doctags', {
        params: {
            'tag': text
        }})
        .then(function (response) {
            let tags = response.data;

            /* Close old autocomplete containers */
            closeAllLists();

            // Current focus for arrow navigation
            currentFocus = -1;

            /* Container for auto complete items */
            let itemContainer = document.createElement("div");
            itemContainer.setAttribute("id", "autocomplete-list");
            itemContainer.setAttribute("class", "autocomplete-items");

            category.parentNode.appendChild(itemContainer);

            /* Appending every auto complete item to container */
            tags.forEach(tag => {
                if (tag['Tag'].substr(0, text.length).toUpperCase() == text.toUpperCase()) {

                    /* DIV element for auto complete item */
                    let item = document.createElement("DIV");

                    /* Making matching letters bold */
                    item.innerHTML = "<strong>" + tag['Tag'].substr(0, text.length) + "</strong>";
                    item.innerHTML += tag['Tag'].substr(text.length);

                    /*insert a input field that will hold the current array item's value:*/
                    item.innerHTML += "<input type='hidden' value='" + tag['Tag'] + "'>";

                    /* Click event listener to select auto complete item */
                    item.addEventListener("click", function(e) {
                        /* Insert selected auto complete item into input field */
                        category.value = this.getElementsByTagName("input")[0].value;

                        /* Close auto complete item container */
                        closeAllLists();
                    });

                    // Appending auto complete item to container
                    itemContainer.appendChild(item);
                }
            });

        })
        .catch(function (error) {
            console.log(error);
        });

});


let currentFocus;

// Arrow navigation
category.addEventListener("keydown", function(e) {
    var x = document.querySelector("#autocomplete-list");
    if (x) x = x.querySelectorAll("div");
    if (e.keyCode === 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
    } else if (e.keyCode === 38) { //up
        /* Decrease current focus if ARROW UP was pressed*/
        currentFocus--;

        /* Mark currently selected item as active */
        addActive(x);
    } else if (e.keyCode === 13) {
        /* Prevent the form from being submitted if ENTER was pressed */
        e.preventDefault();
        if (currentFocus > -1) {
            /* Simulate a click on the "active" item */
            if (x) x[currentFocus].click();
        }
    }
});

function addActive(item) {
    if (!item) return false;

    removeActive(item);
    if (currentFocus >= item.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (item.length - 1);
    item[currentFocus].classList.add("autocomplete-active");
}

function removeActive(items) {
    /* Remove autocomplete-active class from each item */
    items.forEach(item => {
        item.classList.remove('autocomplete-active');
    })
}

function closeAllLists(element) {
    var lists = document.querySelectorAll(".autocomplete-items");

    // Remove each autocomplete list if it's not a given element
    lists.forEach(list => {
       if (list !== element && element !== category) {
           list.parentNode.removeChild(list);
       }
    });
}

document.addEventListener("click", (e) => {
    closeAllLists(e.target);
});

/* Search category END */



