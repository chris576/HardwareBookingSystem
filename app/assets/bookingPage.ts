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
        appendTimeSlots()
        appendBookables()
    }
})

const appendTimeSlots = () => {
    const booking_length_element = document.getElementById('booking_length') as HTMLInputElement | null
    const booking_length = booking_length_element ? parseInt(booking_length_element.value) : 1
    const bookables_table = document.getElementById('bookable_table') as HTMLTableElement
    bookables_table.remove()
    let row: HTMLTableRowElement
    for (let index = 0; index < 24; index += booking_length) {
        if (index % (booking_length * 4) === 0) {
            row = bookables_table.insertRow()
        }
        const bookable = document.createElement('input') as HTMLInputElement
        bookable.classList.add('bookable')
        bookable.type = 'radio'
        const ident = JSON.stringify({
            startDateTime: new Date(dateSelektor.value + ' ' + index + ':00:00'),
            endDateTime: new Date(dateSelektor.value + ' ' + index + booking_length + ':00:00')
        })
        bookable.value = ident
        bookable.id = ident
        bookable.textContent = index + ':00' + ' bis ' + index + bookable + ':00'
        bookable.disabled = true
        const cell = row.insertCell()
        cell.appendChild(bookable)
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
        const bookables = document.getElementsByClassName('bookable') as HTMLCollectionOf<HTMLInputElement>
        let index = 0
        while (index < bookables.length) {
            const bookable = bookables.item(index)
            let b_slot = Object.entries(response.data).find(([key, val]) => { JSON.stringify(val) === bookable.value })
            index++
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