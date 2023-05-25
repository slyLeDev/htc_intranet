import React, {useState, useCallback, useEffect, useLayoutEffect} from 'react';
import { Calendar, Views, momentLocalizer } from 'react-big-calendar'
import moment from "moment";
import "moment/locale/fr"
//import events from "./ressources/events"
import Axios from "axios";
import Routing from "../../js/utils/routing/routing";
import 'bootstrap-datepicker';
import "select2/dist/js/select2.min"

import "jquery-mask-plugin/dist/jquery.mask.min"

const localizer = momentLocalizer(moment);
const now = new Date()
const scrollToTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 7)
const modalCalendarEvent = $('#modal-calendar-event')
const modalCalendarEventContent = $('#js-calendar-event-body')
const btnCreateEvent = $('#js-btn-create-event')
//const formEvent = $('#form_interview_event')

const messages = {
    allDay: 'Dia Inteiro',
    previous: '<',
    next: '>',
    today: "Aujourd'hui",
    month: 'Mois',
    week: 'Semaine',
    work_week: 'Semaine',
    day: 'Jour',
    agenda: 'Listes des évènements',
    date: 'Date',
    time: 'Heure',
    event: 'Evènement',
    showMore: (total) => `+ (${total}) Evènements`,
}

const formats = {
    eventTimeRangeFormat: () => {
        return "";
    },
};

const initDatePicker = () => {
    let _datePickerElement = $('.datepicker');
    _datePickerElement.datepicker({
        autoclose: true,
        language: 'fr',
        format: 'dd/mm/yyyy',
        todayHighlight: true
    });
}

const manageDateEvent = (dateStart, dateEnd) => {
    let eventDateStart = $('#interview_dateStart')
    let eventHourStart = $('#interview_hourStart')
    let eventDateEnd = $('#interview_dateEnd')
    let eventHourEnd = $('#interview_hourEnd')
    eventDateStart.mask('00/00/00')
    eventDateEnd.mask('00/00/00')
    eventHourStart.mask('00:00')
    eventHourEnd.mask('00:00')
    eventDateStart.val(dateStart.getDate()+'/'+('0' + (dateStart.getMonth()+1)).slice(-2)+'/'+dateStart.getFullYear())
    eventHourStart.val(('0' + dateStart.getHours()).slice(-2)+':'+('0'+dateStart.getMinutes()).slice(-2))
    eventDateEnd.val(dateEnd.getDate()+'/'+('0' + (dateEnd.getMonth()+1)).slice(-2)+'/'+dateEnd.getFullYear())
    eventHourEnd.val(('0' + dateEnd.getHours()).slice(-2)+':'+('0'+dateEnd.getMinutes()).slice(-2))

    initDatePicker()
}

const hexColor = '#a6dff9'
//let fake = [{"id":1,"title":"Test event #1","start":{"date":"2023-05-26 07:15:00.000000","timezone_type":3,"timezone":"UTC"},"end":{"date":"2023-05-26 07:45:00.000000","timezone_type":3,"timezone":"UTC"},"hexColor":"#008de9"}];

export default function (props) {
    const [myEvents, setEvents] = useState()

    useEffect(() => {
        fetchAllEvent()
        actionBtnCreateEvent()
    }, [])

    const fetchAllEvent = () => {
        Axios.get(Routing.generate('htcintranet_interview_fetch_all_event'))
            .then(res => setEvents(res.data.events.map((event) =>(
                {
                    id: event.id,
                    title: event.title,
                    start: new Date(event.start.date),
                    end: new Date(event.end.date),
                    hexColor: event.hexColor
                })
            )))
            .catch(err => console.error(err))
    }

    const actionBtnCreateEvent = function () {
        btnCreateEvent.on('click', function () {
            let nowString = (new Date()).toDateString()
            createEventAction(nowString, nowString)
        })
    }

    const createEventAction = function (start, end) {
        modalCalendarEventContent.html('Chargement... <i class="fas fa-cog fa-spin fa-3x" style="font-size: 20px"></i>')

        modalCalendarEvent.modal('show')

        Axios.get(Routing.generate('htcintranet_interview_form_event'))
            .then(res => {
                let dateStart = new Date(start)
                let dateEnd = new Date(end)
                modalCalendarEventContent.html(res.data.content)

                setTimeout(function () {
                    manageDateEvent(dateStart, dateEnd)
                    /*$('#interview_profile').select2({
                        theme: 'bootstrap4',
                    })*/
                }, 500)
            }).then(function (response) {
            onSubmitFormEvent()
        });
    }

    const onSubmitFormEvent = function () {
        let formEvent = $('#form_interview_event')
        formEvent.on('submit', function (event) {
            event.preventDefault()
            $.ajax({
                url: formEvent.prop('action'),
                method: formEvent.prop('method'),
                data: formEvent.serialize(),
            }).done(function (response) {
                if (response.success) {
                    setEvents((prev) => [...prev, {
                        id: response.event.id,
                        title: response.event.title,
                        start: new Date(response.event.start.date),
                        end: new Date(response.event.end.date),
                        hexColor: response.event.hexColor
                    }])

                    modalCalendarEvent.modal('hide')
                } else {
                    console.log('erreur lors de la création de l\'evènement !')
                }
            })
        })
    }

    const handleSelectSlot = useCallback(
        ({ start, end }) => {
            createEventAction(start, end)
        },
        [setEvents]
    )

    const handleSelectEvent = useCallback(
        (event) => window.alert(event.title),
        []
    )

    const eventStyleGetter = (event) => {
        let style = {
            backgroundColor: event.hexColor,
            borderRadius: '5px',
            opacity: 1,
            color: event.textColor,
            border: '0px',
            display: 'block',
            fontSize: '12px',
            fontWeight: 'bold',
            paddingTop: '5px'
        };

        return {
            style: style
        };
    }

    if (myEvents === []) {
        return <div />
    }

    return <div>
        <Calendar
            defaultView={'work_week'}
            views={['day', 'work_week', 'month', 'agenda']}
            messages={messages}
            selectable
            localizer={localizer}
            events={myEvents}
            startAccessor="start"
            endAccessor="end"
            popup={true}
            showAllEvents={true}
            step={15}
            timeslots={4}
            scrollToTime={scrollToTime}
            style={{height: 850}}
            onSelectEvent={handleSelectEvent}
            onSelectSlot={handleSelectSlot}
            eventPropGetter={eventStyleGetter}
            formats={formats}
        />
    </div>
}