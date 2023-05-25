import '../../common';
import Axios from "axios";
import Routing from '../../utils/routing/routing';
import markJS from "../../utils/markjs/mark";
import meilisearch from "../../utils/meilisearch/meilisearch";
import "../../utils/chosen-js/chosen";
import "../../utils/bootstrap/bootstrap-datepicker";

const _contentBodySearchReceived = $('#content-body-search-received');
const _searchInReceivedProfileInput = $('#search_in_received_profile');
const _countResultText = $('#count-result-stat');
const _previousButton = $('#previous_button');
const _nextButton = $('#next_button');
const _infoPage = $('#info-page');
const _searchProfilePerPage = $('#search_profile_per_page');
const _searchProfileOrderBy = $('#search_profile_order_by');
const _searchProfileOrderByDirection = $('#search_profile_order_by_direction');
const _searchProfileStatus = $('#search_profile_status');
const _searchProfileXPYear = $('#search_profile_xp_year');
const _searchProfileSectors = $('#search_profile_sector');
const _isReceived = $('#is-profile-received');
const _overlay = $('.overlay');

const elementActions = [
    {
        'element': _searchInReceivedProfileInput,
        'action': 'keyup'
    },
    {
        'element': [
            _searchProfilePerPage,
            _searchProfileOrderBy,
            _searchProfileOrderByDirection,
            _searchProfileXPYear,
            _searchProfileStatus,
            _searchProfileSectors
        ],
        'action': 'change'
    }
]

const controller = new AbortController();

const profile = {
    init: () => {
        let _showProfileSheetClass = $('.js-show-profile-sheet');
        let _modalContent = $('#js-profile-sheet-body');

        _showProfileSheetClass.click(function(e) {
            e.preventDefault();
            _modalContent.html(''); //clear modal content first
            let _profileId = $(this).data('id');
            Axios.get(Routing.generate('htcintranet_admin_profile_sheet', {id: _profileId}))
                .then(res => {
                    _modalContent.html(res.data.sheet);
                }).then(function (response) {
                console.log(response);
            });
        })

        _previousButton.on('click', function () {
            meilisearch.updatePageNumber(this)
            profile.search()
        })

        _nextButton.on('click', function () {
            meilisearch.updatePageNumber(this)
            profile.search()
        })

        elementActions.forEach(function (value) {
            if (Array.isArray(value.element)) {
                value.element.forEach(function (innerValue) {
                    innerValue.on(value.action, function () {
                        profile.doSearch()
                    })
                })
            } else {
                value.element.on(value.action, function () {
                    profile.doSearch()
                })
            }
        })
    },

    doSearch: () => {
        meilisearch.resetPage()
        profile.search()
    },

    applyDatepicker: () => {
        $('#search_profile_received_at').datepicker().on('changeDate', function (selected) {
            let minDate = new Date(selected.date.valueOf());
            $('#search_profile_received_to').datepicker('setStartDate', minDate);
        });

        $('#search_profile_received_to').datepicker().on('changeDate', function (selected) {
            let maxDate = new Date(selected.date.valueOf());
            $('#search_profile_received_at').datepicker('setEndDate', maxDate);
        });
    },

    payload: () => {
        let _querySearch = _searchInReceivedProfileInput.val()
        let _pageNumber = parseInt(document.querySelector('.pagination_search').dataset.pageNumber)
        let _perPage = parseInt(_searchProfilePerPage.val())
        let _orderBy = _searchProfileOrderBy.val()
        let _orderByDirection = _searchProfileOrderByDirection.val()
        let _xpYear = _searchProfileXPYear.val()
        let _profileStatus = _searchProfileStatus.val()
        let _profileSectors = _searchProfileSectors.val()
        let _justProfileReveived = _isReceived.val()

        return {
            querySearch: _querySearch.trim(),
            pageNumber: _pageNumber,
            limitPerPage: _perPage,
            orderBy: _orderBy,
            orderByDirection: _orderByDirection,
            xpYear: _xpYear,
            status: _profileStatus,
            sectors: _profileSectors,
            justReceived: _justProfileReveived
        }
    },

    search: () => {
        controller.abort() // try cancel the request
        _overlay.removeClass('d-none')
        _previousButton.prop('disabled', true)
        _nextButton.prop('disabled', true)
        _previousButton.addClass('d-none')
        _nextButton.addClass('d-none')
        _infoPage.addClass('d-none')

        Axios.post(Routing.generate('htcintranet_admin_profile_search', profile.payload()))
            .then(res => {
                _countResultText.html(`<span style="font-size: 13px;"><b>${res.data.count} résultat${res.data.count > 0 ? 's' : ''}</span><small class="text-gray-700" style="font-size: 11px;"> </b> en <b>${res.data.perf} ms</b> </small>`)
                _contentBodySearchReceived.html(res.data.resultSearch);
                if (res.data.showPreviousButton) {
                    _previousButton.prop('disabled', false)
                }

                if (res.data.showNextButton) {
                    _nextButton.prop('disabled', false)
                }

                if (res.data.totalPage > 1) {
                    _infoPage.removeClass('d-none')
                    _previousButton.removeClass('d-none')
                    _nextButton.removeClass('d-none')
                    _infoPage.html(`Page ${res.data.pageNumber + 1} / ${res.data.totalPage}`)
                }

                setTimeout(function () {
                    /*let _equalHeight = $('.jQueryEqualHeight');
                    _equalHeight.jQueryEqualHeight('.received-card-item-general-info');
                    _equalHeight.jQueryEqualHeight('.received-card-item-sector');
                    _equalHeight.jQueryEqualHeight('.received-card-item-received-at');
                    _equalHeight.jQueryEqualHeight('.received-list-action');*/

                    markJS.highlight('.received-card-item-general-info', res.data.querySearch) //highlight keyword result
                    _overlay.addClass('d-none')
                }, 500)
            }).then(function (response) {

        });
    },
}

$(function () {
    profile.init()
    meilisearch.resetPage()
    profile.search()
    profile.applyDatepicker()
});