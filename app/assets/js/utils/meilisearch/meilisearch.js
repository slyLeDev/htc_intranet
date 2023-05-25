const meilisearch = {
    updatePageNumber: (elem) => {
        const directionBtn = elem.id
        // Get the page number stored in the pagination element
        let pageNumber = parseInt(document.querySelector('.pagination_search').dataset.pageNumber)

        // Update page number
        if (directionBtn === 'previous_button') {
            pageNumber = pageNumber - 1
        } else if (directionBtn === 'next_button') {
            pageNumber = pageNumber + 1
        }

        // Store new page number in the pagination element
        document.querySelector('.pagination_search').dataset.pageNumber = pageNumber
    },

    resetPage: () => {
        // Add data to our HTML element stating the user is on the first page
        document.querySelector('.pagination_search').dataset.pageNumber = 0
    }
}

export default meilisearch;