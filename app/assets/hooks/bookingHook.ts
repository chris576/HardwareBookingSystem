import { useMutation, useQuery } from '@tanstack/react-query'
import axios from 'axios'
import { useAsyncValue } from 'react-router-dom'

/**
 * @param hardwareId The of the hardware to book.
 * @param date The date of wich the hardware should be booked.
 * @param slotSize The slots size 
 */
export const useReservedTimes = (hardwareId: number, date: Date) => {

    const connectionString = 'http://localhost:80/api/booking'

    const getNotBookable = useQuery({
        queryKey: [{ hardwareId: hardwareId, date: date }],
        queryFn: async ({ queryKey }) => {
            const [{ hardwareId, date }] = queryKey;
            return await axios.get(connectionString + '/reserved', {
                params: {
                    hardwareId: hardwareId, 
                    date: date 
                }
             })
        }
    })

    return getNotBookable
}

export const usedPostBooking = () => {
    
    const connectionString = 'http://localhost:80/api/booking'

    const postBooking = useMutation({
        mutationFn: async (newBooking: { hardwareId: number, start: Date, end: Date }) => {
            return await axios.post(connectionString + '/post', { 
                hardwareId: newBooking.hardwareId, 
                start: newBooking.start,
                end: newBooking.end })
        }
    })

    return postBooking
}