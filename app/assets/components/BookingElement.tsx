import {useState} from 'react'
import React from 'react'
import {useHardware} from '../hooks/hardwareHook'
import Form from 'react-bootstrap/Form'
import {Button, Col, Container, Row} from 'react-bootstrap'
import {usePostBooking} from '../hooks/bookingHook'
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import 'bootstrap/dist/css/bootstrap.min.css';

export default function BookingElement() {

    const {data} = useHardware()
    const [hardwareId, setHardwareId] = useState<number>()
    const [startDate, setStartDate] = useState<Date>()
    const [endDate, setEndDate] = useState<Date>()
    const {mutate} = usePostBooking()

    const setHours = (hours: number, dateObj: Date) => {
        const date = new Date(dateObj)
        date.setHours(hours)
        return date
    }

    const handlePostBooking = () => {
        mutate({
            hardwareId: hardwareId,
            start: startDate,
            end: endDate
        })
    }

    return (
        <Container fluid>
            <Row>
                <Col>
                    <Form.Select
                        required
                        onChange={evt => setHardwareId(parseInt(evt.target.value))}>
                        <option>Select an Hardware</option>
                        {data?.map(item => <option value={item.id}>{item.name}</option>)}
                    </Form.Select>
                </Col>
                <Col>
                    <Form.Label>Datum</Form.Label>
                    <DatePicker
                        selected={startDate}
                        onChange={(date) => {
                            date.setHours(23)
                            setStartDate(date)
                            setEndDate(date)
                        }}
                    />
                </Col>
                <Col>
                    <Form.Label>Anfangszeit</Form.Label>
                    <DatePicker
                        selected={startDate}
                        onChange={(date) => {
                            setStartDate(date)
                        }}
                        disabled={startDate == null && endDate == null}
                        minTime={setHours(0, startDate)}
                        maxTime={endDate}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={60}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                    />
                </Col>
                <Col>
                    <Form.Label>Endzeitpunkt</Form.Label>
                    <DatePicker
                        selected={endDate}
                        minTime={startDate}
                        maxTime={setHours(23, endDate)}
                        onChange={(date) =>
                            setEndDate(date)
                        }
                        disabled={startDate == null && endDate == null}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={60}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                    />
                </Col>
                <Col>
                    <Button variant="primary"
                            onClick={handlePostBooking}
                            disabled={hardwareId == null || startDate == null || endDate == null}>
                        Buchen
                    </Button>
                </Col>
            </Row>
        </Container>
    );
}