import {useEffect, useState} from 'react';
import Select from 'react-select'

export default function Searchbar() {

    const [hardwareOptions, setHardwareOptions] = useState(null);

    useEffect(() => {
        fetch('/api/hardware')
            .then(res => {
                return res.json()
            })
            .then((data) => {
                setHardwareOptions(data)
                console.log(data)
            })
    }, []);

    return (
        <>
            <Select options={hardwareOptions} />
        </>
    );
}