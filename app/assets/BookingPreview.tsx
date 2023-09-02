import React from 'react';
import {QueryClient, QueryClientProvider} from "@tanstack/react-query";
import BookingElement from "./components/BookingElement";

const queryClient = new QueryClient()

export function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <BookingElement />
        </QueryClientProvider>
    );
}