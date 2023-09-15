import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios';

const hardwareSelektor = document.getElementById('hardware') as HTMLInputElement;
const dateSelektor = document.getElementById('date') as HTMLInputElement;

document.getElementById("booking_form").addEventListener("submit", function (event) {
    event.preventDefault();
    const dateTimeSelektor = document.getElementById('bookables') as HTMLInputElement;
    const dateRangeJSON = JSON.parse(dateTimeSelektor.value);
    axios.post('/api/booking/create', {
        hardware: hardwareSelektor.value,
        startDateTime: dateRangeJSON.start,
        endDateTime: dateRangeJSON.end
    })
});

const axiosGet = () => {
    axios({
        method: 'GET',
        url: '/api/booking/create',
        params: {
            date: dateSelektor.value,
            hardware: hardwareSelektor.value
        }
    }).then((response) => {
    })
}

hardwareSelektor.addEventListener('input', (event) => {
    const date = dateSelektor.value
    if (date != null) {
        axiosGet()
    }
});

dateSelektor.addEventListener('input', (event) => {
    const hardware = hardwareSelektor.value;
    if (hardware != null) {
        axiosGet()
    }
});