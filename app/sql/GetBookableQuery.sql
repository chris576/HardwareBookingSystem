WITH RECURSIVE MyHours AS (
    SELECT :bookingDate AS myTimestamp
    UNION ALL
    SELECT DATE_ADD(myTimestamp, INTERVAL 1 HOUR)
    FROM MyHours
    WHERE DATE_ADD(myTimestamp, INTERVAL 1 HOUR) <= DATE_ADD(
            :bookingDate,
            INTERVAL 24 - EXTRACT(
                HOUR
                FROM :bookingDate
            ) HOUR
        )
)
SELECT DISTINCT *
FROM MyHours AS h1,
    MyHours as h2,
    booking
WHERE
    /** Alle Zeiten, wo die Differenz von r1 und endTime der Buchungsdauer in Stunden ist.  */
    TIMESTAMPDIFF(HOUR, h1.myTimestamp, h2.myTimestamp) = :bookingLength
    AND hardware_id = :hardwareId
    AND (
        Date(h1.myTimestamp) = Date(start_date)
        AND Date(h2.myTimestamp) = Date(end_date)
    )
    AND (
        h1.myTimestamp not between start_date AND end_date
        OR h2.myTimestamp not between start_date AND end_date
    )
    AND (
        start_date NOT BETWEEN h1.myTimestamp AND h2.myTimestamp
        OR end_date NOT BETWEEN h1.myTimestamp AND h2.myTimestamp
    );
CALL calculateBookables ('2023-09-26 00:00:00', 1, 1);

SELECT COUNT(*) FROM booking
WHERE DATE(:bookingDate) = DATE(start_date)
AND :hardwareId = hardware_id;