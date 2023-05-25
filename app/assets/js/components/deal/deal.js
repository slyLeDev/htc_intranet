import '../../common';
import Axios from "axios";
import Routing from '../../utils/routing/routing';
import 'bootstrap-datepicker';
import tinymce from '../../utils/tinymce/tinymce.min';
import htcToast from "../../utils/toasts/toast";

const deal = {
    init: () => {
        let _showDealSheetClass = $('.js-show-deal-sheet');
        let _modalContent = $('#js-deal-sheet-body');

        _showDealSheetClass.click(function(e) {
            e.preventDefault();
            _modalContent.html(''); //clear modal content first
            let _dealId = $(this).data('id');
            Axios.get(Routing.generate('htcintranet_deal_sheet', {id: _dealId}))
                .then(res => {
                    _modalContent.html(res.data.sheet);
                }).then(function (response) {
                    console.log(response);
                });
        })
    },

    initDatePicker: () => {
        let _datePickerElement = $('.datepicker');
        _datePickerElement.datepicker({
            autoclose: true,
            language: 'fr',
            format: 'dd/mm/yyyy',
            todayHighlight: true
        });
    },

    initTinyMCE: () => {
        tinymce.init({
            selector: 'textarea.tinymce',
            plugins: [
                'advlist','autolink',
                'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
                'fullscreen','insertdatetime','media','table','help','wordcount'
            ],
            toolbar: 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify |' +
                'bullist numlist checklist outdent indent | removeformat | code table help'
        });
    },

    hookTinyMCE: () => {
        setTimeout(function () {
            let _toxPromotionUpgrade = $('.tox-promotion');
            _toxPromotionUpgrade.addClass('d-none').hide()
        }, 300);
    },

    manageSalary: () => {
        $(document).on('click', 'input[name="deal[salaryState]"]', function () {
            let _theValue = $('input[name="deal[salaryState]"]:checked').val();
            let _blockSalaryVariable = $('#salary-variable');
            let _blockSalaryExact = $('#salary-exact');
            let _dealSalaryMin = $('#deal_salaryMin');
            let _dealSalaryMax = $('#deal_salaryMax');
            let _dealSalaryExact = $('#deal_salaryExact');
            if (_theValue === '0') {
                _blockSalaryExact.addClass('d-none').hide();
                _blockSalaryVariable.removeClass('d-none').show();
                _dealSalaryMin.attr('required', true);
                _dealSalaryMax.attr('required', true);
                _dealSalaryExact.attr('required', false);
            } else {
                _blockSalaryVariable.addClass('d-none').hide();
                _blockSalaryExact.removeClass('d-none').show();
                _dealSalaryMin.attr('required', false);
                _dealSalaryMax.attr('required', false);
                _dealSalaryExact.attr('required', true);
            }
        })
    },

    downloadDealFile: () => {
        let _downloadButton = $('#download-deal');
        _downloadButton.click(function (e) {
            e.preventDefault();
            let _href = $(this).attr('href');
            let _filename = $(this).data('filename');
            let _link = document.createElement('a');
            document.body.appendChild(_link);
            _link.setAttribute('type', 'hidden');
            _link.setAttribute('download', true);
            _link.href = _href;
            _link.download = _filename;
            _link.click();
            _link.remove();

            //toast
            let _toastLive = $('#liveToast');
            let _toastBody = _toastLive.find('.toast-body');
            _toastBody.text('Téléchargement effectué !');
            htcToast.show();
        })
    },

    auto: () => {
        let _salaryStateChecked = $('input[name="deal[salaryState]"]:checked');
        if (0 === _salaryStateChecked.length) {
            let _firstStateChoice = $('input[name="deal[salaryState]"]')[0];
            if (_firstStateChoice !== undefined) {
                _firstStateChoice.click()
            }
        } else {
            _salaryStateChecked.click()
        }
    }
}

$(function () {
    deal.init()
    deal.initDatePicker()
    deal.initTinyMCE()
    deal.manageSalary()
    deal.auto()
    deal.hookTinyMCE()
    deal.downloadDealFile()
});