import '../../common';

const user = {
    onNewEdit: () => {
        let _form = $("#form_user");
        _form.on('submit', function () {
            let _errorFormRegister = $('#error_form_register');
            _errorFormRegister.addClass('d-none');
            if ($("#user_password").val() !== $("#verifpass").val()) {
                _errorFormRegister.text('Les deux mots de passe saisies sont différents. Merci de renouveler l\'opération !');
                _errorFormRegister.removeClass('d-none');
                return false;
            }
        })
    },
    changeState: () => {
        let _changeStateClass = $('.js-enable-disable-state');
        let _modal = $('#modal-state-confirmation');
        let _modalTitle = $('#js-confirmation-state-title');
        let _modalContent = $('#js-confirmation-state-content');

        _changeStateClass.click(function(e) {
            e.preventDefault();
            let _isEnable = $(this).data('state');
            let _href = $(this).attr('href');
            if (_isEnable) {
                _modalTitle.text('Désactivation');
                _modalContent.text('Etes-vous sûr de vouloir désactiver ce consultant ?');
            } else {
                _modalTitle.text('Activation');
                _modalContent.text('Etes-vous sûr de vouloir activer ce consultant ?');
            }

            _modal.on('click', '.js-btn-confirm', function (e) {
                window.location.href = _href;
            });
        })
    }
}

$(function () {
    user.onNewEdit()
    user.changeState()
});