import React from 'react';
import ReactDOM from 'react-dom';
import { QueryClient, QueryClientProvider, useQuery } from '@tanstack/react-query';
import App from './app-preview';

const queryClient = new QueryClient()

ReactDOM.render(
    <App />,  
    document.getElementById('root'));