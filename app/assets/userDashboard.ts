const axios = require('axios')

const cancelButtons = document.getElementsByClassName('cancel-booking-btn');

const postCancelBooking = (evt) => {
    const id = evt.target.id
    axios.post('/api/booking/delete/' + id)
        .then(() => {
            evt.target.parent.remove()
        })
}

for (let i = 0; i < cancelButtons.length; i++) {
    cancelButtons.item(i).addEventListener('click', postCancelBooking)
}