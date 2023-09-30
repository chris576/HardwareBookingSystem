import axios from 'axios'

const hardwareSelektor = document.getElementById('hardware') as HTMLSelectElement
const dateSelektor = document.getElementById('date') as HTMLInputElement
const lengthSelektor = document.getElementById('booking_length') as HTMLInputElement | null

hardwareSelektor.addEventListener('input', function () {
    if (hardwareSelektor.value) {
        appendBookables()
    }
})

dateSelektor.addEventListener('input', function () {
    if (hardwareSelektor.value) {
        appendBookables()
    }
})

lengthSelektor?.addEventListener('input', function () {
    if (hardwareSelektor.value) {
        appendBookables()
    }
})

const appendTimeSlots = () => {
    const booking_length_element = document.getElementById('booking_length') as HTMLInputElement | null
    const booking_length = booking_length_element ? parseInt(booking_length_element.value) : 1
    const bookables_root = document.getElementById('bookables_root') as HTMLDivElement
    bookables_root.replaceChildren()
    for (let index = 0; index < 24; index+=booking_length) {

    }
}

const appendBookables = () => {
    const booking_length = document.getElementById('booking_length') as HTMLInputElement | null
    const params = booking_length ?
        {
            date: dateSelektor.value,
            hardware: hardwareSelektor.value,
            booking_length: booking_length ? booking_length.value : 1
        }
        : {
            date: dateSelektor.value,
            hardware: hardwareSelektor.value,
        }
    axios({
        method: 'GET',
        url: '/api/booking/create',
        params
    }).then((response) => {
        const bookables_root = document.getElementById('bookables_root') as HTMLDivElement
        bookables_root.replaceChildren()
        for (const [key, val] of Object.entries(response.data)) {
            const bookable = document.createElement('input') as HTMLInputElement
            bookable.type = "radio"
            bookable.name = "bookable_select"
            bookable.value = JSON.stringify(val)
            bookable.textContent = new Date(JSON.parse(JSON.stringify(val)).startDateTime).getHours() + ' bis ' + new Date(JSON.parse(JSON.stringify(val)).endDateTime).getHours()
            bookables_root.appendChild(bookable)
        }
    }).catch((error) => {
    })
}

document.getElementById("booking_form").addEventListener("submit", function (event) {
    event.preventDefault()
    postBooking()
});

const postBooking = () => {
    const hardwareElement = document.getElementById('hardware') as HTMLSelectElement
    const bookingElement = document.getElementById('bookables') as HTMLSelectElement
    axios.post('/api/booking/create', {
        startDateTime: JSON.parse(bookingElement.value).startDateTime,
        endDateTime: JSON.parse(bookingElement.value).endDateTime,
        hardware: parseInt(hardwareElement.value)
    })
}