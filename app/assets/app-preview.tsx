import React from 'react'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import BookingElement from './components/BookingElement'
import 'bootstrap/dist/css/bootstrap.min.css'

const queryClient = new QueryClient()

function App() {
  return (
    <QueryClientProvider client={queryClient}>
        <BookingElement />
    </QueryClientProvider>
  );
}

export default App;
