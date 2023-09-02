import {useQuery} from '@tanstack/react-query'
import axios from 'axios';

export const useHardware = (hardwareID: number = null) => {
    const connectionString = (hardwareID == null) ? 'http://localhost:80/api/hardware' : `'http://localhost:80/api/hardware/${hardwareID}`
    return useQuery({
        queryFn: async () => {
            const {data} = await axios.get(connectionString)
            return (hardwareID) == null ? data['hydra:member'] as Array<{
                id: number,
                name: string
            }> : [data] as Array<{ id: number, name: string }>
        }
    })
}