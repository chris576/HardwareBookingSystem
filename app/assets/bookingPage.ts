const hardwareSelektor = document.getElementById('hardware') as HTMLSelectElement
const dateSelektor = document.getElementById('date') as HTMLInputElement
const lengthSelektor = document.getElementById('booking_length') as HTMLInputElement | null

const parameters = () => {
    let rtn = "?hardware=" + hardwareSelektor.value + "&date=" + dateSelektor.value
    if (lengthSelektor != null) {
        rtn += "&booking_length=" + lengthSelektor.value
    }
    return rtn
}

hardwareSelektor.addEventListener('change', function () {
    window.location.href = "/api/booking/read" + parameters()
})

dateSelektor.addEventListener('change', function () {
    window.location.href = "/api/booking/read" + parameters()
})

lengthSelektor?.addEventListener('change', function () {
    window.location.href = "/api/booking/read" + parameters()
})