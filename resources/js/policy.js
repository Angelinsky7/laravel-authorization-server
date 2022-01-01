import * as Popper from '@popperjs/core';

window.Popper = Popper;

window.policy = {
    tableSelect: require('./shared/table-select'),
    modal: require('./shared/modal'),
    // navigable: require('./shared/navigable'),
}
