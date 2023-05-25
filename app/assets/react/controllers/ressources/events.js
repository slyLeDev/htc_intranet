const now = new Date()

export default [
    {
        id: 14,
        title: 'Today',
        start: new Date(new Date().setHours(new Date().getHours() - 3)),
        end: new Date(new Date().setHours(new Date().getHours() + 3)),
        hexColor: '#008de9',
    },
    {
        id: 15,
        title: 'Point in Time Event',
        start: now,
        end: now,
        hexColor: '#008de9',
    }
]