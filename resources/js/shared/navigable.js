module.exports = createModal;

var createModal = function (modal_content, container) {
    var modal = document.createElement("div");
    var id = (+container.getAttribute("modals-count")) + 1;
    container.setAttribute("modals-count", id);
    modal.id = "modal_" + id;
    modal.className = "modal";
    modal.innerHTML = modal_content;
    container.append(modal);

    Alpine.discoverUninitializedComponents((el) => {
        Alpine.initializeComponent(el)
    }, container)
};

(function init() {
    document.addEventListener("click", (e) => {
        var el = e.target, found;
        while (el && !(found = el.matches("[data-navigable]"))) {
            el = el.parentElement;
        }
        if (found) {
            e.preventDefault();

            var link_href = el.href;
            window.location.href = link_href;

            return false;
        }
    })
})();
