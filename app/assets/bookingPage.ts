import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios'

const hardwareSelektor = document.getElementById('hardware') as HTMLInputElement
const dateSelektor = document.getElementById('date') as HTMLInputElement
const bookables = document.getElementById('bookables') as HTMLUListElement

document.getElementById("booking_form").addEventListener("submit", function (event) {
    event.preventDefault();
    const dateTimeSelektor = document.getElementById('bookables') as HTMLInputElement
    const dateRangeJSON = JSON.parse(dateTimeSelektor.value);
    axios.post('/api/booking/create', {
        hardware: hardwareSelektor.value,
        startDateTime: dateRangeJSON.start,
        endDateTime: dateRangeJSON.end
    })
});

const JSONtoLI = (json) => {
    const element = document.createElement('li') as HTMLLIElement
    element.value = json
    element.textContent = json.start
    element.classList.add('list-group-item')
    element.addEventListener('click', (ev) => {
        const activeItems = bookables.getElementsByClassName('active')
        for (let i = 0; i < activeItems.length; i++) {
            const item = activeItems.item(i)
            item.classList.remove('active')
        }
        element.classList.add('active')
    })
    return element;
}

const getBookables = () => {
    axios({
        method: 'GET',
        url: '/api/booking/create',
        params: {
            date: dateSelektor.value,
            hardware: hardwareSelektor.value
        }
    }).then((response) => {
        while (bookables.children.length > 0) {
            const item = bookables.children.item(0)
            bookables.removeChild(item);
        }
        for (const [key, val] of Object.entries(response.data)) {
            bookables.appendChild(JSONtoLI(val))
        }
    })
}

hardwareSelektor.addEventListener('input', (event) => {
    const date = dateSelektor.value
    if (date != null && hardwareSelektor.value != null) {
        getBookables()
        const submit = document.getElementById("submit") as HTMLInputElement
        submit.disabled = false
    } else {
        const submit = document.getElementById("submit") as HTMLInputElement
        submit.disabled = true
    }
});

dateSelektor.addEventListener('input', (event) => {
    const hardware = hardwareSelektor.value
    if (hardware != null && dateSelektor.value != null) {
        getBookables()
        const submit = document.getElementById("submit") as HTMLInputElement
        submit.disabled = false
    } else {
        const submit = document.getElementById("submit") as HTMLInputElement
        submit.disabled = true
    }
});