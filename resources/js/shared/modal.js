module.exports = {
    createModal: createModalImpl,
    closeModal: closeModalImpl
};

var createModalImpl = function (modal_content, container) {
    var modal = document.createElement("div");
    var id = (+container.getAttribute("modals-count")) + 1;
    container.setAttribute("modals-count", id);
    modal.id = "modal_" + id;
    modal.className = "modal modal-container";
    modal.innerHTML = modal_content;
    container.append(modal);

    // Alpine.discoverUninitializedComponents((el) => {
    //     Alpine.initializeComponent(el)
    // }, container)
};

var closeModalImpl = function (modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
};

(function init() {
    var modals = document.createElement("div");
    modals.id = "modals-container";
    document.body.append(modals);

    document.addEventListener("click", (e) => {
        var el = e.target, found;
        while (el && !(found = el.matches("[data-remote][data-modal]"))) {
            el = el.parentElement;
        }
        if (found) {
            e.preventDefault();

            var link_href = el.href;
            if (link_href == null) {
                let formData = new FormData(el);
                let search = new URLSearchParams(formData);

                link_href = `${el.action}?${search.toString()}`;
                console.log(link_href, el, formData, search);
            }
            if (link_href.indexOf("?") == -1) {
                link_href += "?modal=true";
            } else {
                link_href += "&modal=true";
            }

            fetch(link_href)
                .then(response => response.text())
                .then(html => createModalImpl(html, modals));

            return false;
        }
    })

    document.addEventListener("click", (e) => {
        var el = e.target, found;
        while (el && !(found = el.matches("[data-cancel]"))) {
            el = el.parentElement;
        }
        if (found) {
            e.preventDefault();

            while (el && !(found = el.matches(".modal-container"))) {
                el = el.parentElement;
            }

            if (found) {
                console.log(el);
                closeModalImpl(el.id);
            }

            return false;
        }
    })

})();
