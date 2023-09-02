import React from 'react';
import ReactDOM from 'react-dom';
import {QueryClient, QueryClientProvider} from "@tanstack/react-query";
import BookingElement from "../components/BookingElement";

const queryClient = new QueryClient()

function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <BookingElement />
        </QueryClientProvider>
    );
}

ReactDOM.render(
    <App />,
    document.getElementById('root'));