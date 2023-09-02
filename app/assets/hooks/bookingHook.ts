import {useMutation, useQuery} from '@tanstack/react-query'
import axios from 'axios'

/**
 * @param hardwareId The of the hardware to book.
 * @param date The date of wich the hardware should be booked.
 */
export const useReservedTimes = (hardwareId: number, date: Date) => {
    return useQuery({
        queryKey: [{hardwareId: hardwareId, date: date}],
        queryFn: async ({queryKey}) => {
            const [{hardwareId, date}] = queryKey;
            return await axios.get('/api/booking/reserved', {
                params: {
                    hardwareId: hardwareId,
                    date: date
                }
            })
        }
    })
}

export const usePostBooking = () => {
    return useMutation({
        mutationFn: async (newBooking: { hardwareId: number, start: Date, end: Date }) => {
            const postData = new URLSearchParams();
            postData.append('hardwareId', newBooking.hardwareId.toString());
            postData.append('start', newBooking.start.toISOString());
            postData.append('end', newBooking.end.toISOString());
            return await axios.post(
                '/api/booking/post',
                postData,
                {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }
            )
        }
    })
}