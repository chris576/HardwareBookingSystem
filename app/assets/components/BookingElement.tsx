import {useState} from 'react'
import React from 'react'
import { useHardware, Hardware } from '../hooks/hardwareHook'
import Form from 'react-bootstrap/Form'
import { Col, Container, Row } from 'react-bootstrap'
import { useBooking } from '../hooks/bookingHook'
import { DatePicker } from 'react-datepicker'

export default function BookingElement() {

    const  {getNotBookable, postBooking}  = useBooking()
    const { data } = useHardware()
    const [hardware, setHardware] = useState<Hardware>()
    const [date, setDate] = useState<Date>()
    const [excludedTimes, setExcludedTimes] = useState<Array<Date>>()

    return (
    <Container fluid>
        <Row className="d-flex align-items-stretch">            
            <Col>
                <Form.Select
                    required
                    onChange={ evt => setHardware( data.find( item => item.id == parseInt(evt.target.value) ) ) } >
                    <option>Select an Hardware</option>
                    {  data?.map(item => <option value={item.id}>{item.name}</option>) } 
                </Form.Select>
            </Col>
            <Col>
                <DatePicker
                    showIcon
                    showTimeSelect
                    selected={ date }
                    minDate={ new Date() }
                    timeIntervals={60}
                    onChange={ date =>  {
                        setExcludedTimes()
                        setDate(date) 
                    }}
                    excludeTimes={ excludedTimes } 
                />
            </Col>
        </Row>
    </Container>
    );
}