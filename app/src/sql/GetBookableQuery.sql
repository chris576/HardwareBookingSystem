SET @Startzeit = CAST(
        CONCAT(
            Date(:booking_date),
            ' 00:00:00'
        ) AS DATETIME
    )
SET @Endzeit = DATE_ADD(@Startzeit, Interval 1 DAY)
WITH RECURSIVE MyHours AS (
    SELECT @Startzeit AS myTimestamp
    UNION ALL
    SELECT DATE_ADD(myTimestamp, INTERVAL 1 HOUR)
    FROM MyHours
    WHERE DATE_ADD(myTimestamp, INTERVAL 1 HOUR) <= @Endzeit
)
SELECT h1.myTimestamp as start,
    h2.myTimestamp as end
FROM MyHours AS h1,
    MyHours AS h2,
    booking
    /** Nur diejenigen Buchungen für die gewünschte Hardware dürfen betrachtet werden. */
WHERE hardware_id = :hardwareId
    /** Alle Zeiten die zwischen start und ende liegen, also nicht buchbar sind, werden ignoriert */
    AND (
        h1.myTimestamp not between start_date AND end_date
        OR h2.myTimestamp not between start_date AND end_date
    )
    /** Alle Zeiten, in denen das Start oder Enddatum kleiner oder größer ist als das minimum/maximum von startTime, werden ignoriert. */
    AND (
        start_date between ( SELECT MIN(myTimestamp) FROM MyHours ) AND ( SELECT MAX(myTimestamp) FROM MyHours )
        OR end_date between ( SELECT MIN(myTimestamp) FROM MyHours ) AND ( SELECT MAX(myTimestamp) FROM MyHours )
    )
    /** R und R2 dürfen Zeiten, in denen die Buchung schon belegt ist, nicht überbuchen. */
    AND (
        start_date NOT BETWEEN h1.myTimestamp AND h2.myTimestamp
        OR end_date NOT BETWEEN h1.myTimestamp AND h2.myTimestamp
    )
    /** Alle Zeiten, wo die Differenz von r1 und endTime der Buchungsdauer in Stunden ist.  */
    AND TIMESTAMPDIFF(HOUR, h1.myTimestamp, h2.myTimestamp) = :bookingLength;