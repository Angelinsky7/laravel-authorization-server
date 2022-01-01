const { isObject } = require("lodash");

module.exports = function (itemsCount) {

    var setTickToInput = function (isChecked, root, selection) {
        root.querySelectorAll('input[data-select-input]').forEach(item => {
            item.checked = isChecked;
            if (isChecked) { selection.push(item.dataset.selectInput); }
        });
    };

    return {
        total: itemsCount,
        selection: [],
        reset(root, refs) {
            this.selection = []
            setTickToInput(false, root, this.selection);
            refs.indeterminate = false;
            refs.checked = false;
        },
        toggle(id, refs, isChecked) {
            if (id != null) {
                var indexOf = this.selection.indexOf(id);
                if (indexOf >= 0 && !isChecked) {
                    this.selection.splice(indexOf, 1);
                } else if (isChecked) {
                    this.selection.push(id);
                }
            }

            refs.indeterminate = this.selection.length > 0 && this.selection.length < this.total;
            refs.checked = this.selection.length > 0;
        },
        toggleAll(isChecked, root) {
            this.selection = [];
            setTickToInput(isChecked, root, this.selection);
        }
    };
};
