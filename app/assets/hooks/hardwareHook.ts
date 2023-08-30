import { useQuery } from '@tanstack/react-query'
import axios, {isCancel, AxiosError} from 'axios';

export interface Hardware {
    id: number
    name: string
    description: string 
    ipV4: string
}

export const useHardware = (hardwareID: number = null) => {
    const connectionString = (hardwareID == null) ? 'http://localhost:80/api/hardware' : `'http://localhost:80/api/hardware/${hardwareID}`
    return useQuery({
        queryFn: async () => {
            const { data } = await axios.get(connectionString)
            return (hardwareID) == null ? data['hydra:member'] as Array<Hardware> : [data] as Array<Hardware>
        }
    })
}