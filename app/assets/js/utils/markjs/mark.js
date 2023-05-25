import 'mark.js/dist/jquery.mark.min'

const markJS = {
    highlight: (context, keyword, minLength = 2) => {
        $(context).unmark({
            done: function() {
                if (keyword.length > minLength) {
                    keyword.split(' ').forEach(function (item) {
                        $(context).mark(item)
                    })
                }
            }
        })
    }
}

export default markJS;