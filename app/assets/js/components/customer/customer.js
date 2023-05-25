import '../../common';
import Axios from "axios";
import Routing from '../../utils/routing/routing';
import "../../utils/chosen-js/chosen";

const customer = {
    init: () => {
        let _showCustomerSheetClass = $('.js-show-customer-sheet');
        let _modalContent = $('#js-customer-sheet-body');

        _showCustomerSheetClass.click(function(e) {
            e.preventDefault();
            _modalContent.html(''); //clear modal content first
            let _customerId = $(this).data('id');
            Axios.get(Routing.generate('htcintranet_customer_sheet', {id: _customerId}))
                .then(res => {
                    _modalContent.html(res.data.sheet);
                }).then(function (response) {
                    console.log(response);
                });
        })
    }
}

$(function () {
    customer.init()
});