import { QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import React from 'react';
import { HashRouter } from 'react-router-dom';
import Header from '../@/components/layout/header';
import Sidebar from '../@/components/layout/sidebar';
import { ScrollArea } from '../@/components/ui/scroll-area';
import { Toaster } from '../@/components/ui/toaster';
import { reactQueryClient } from './configs/react-query';
import { Router } from './router/Router';

export const App = () => {
	return (
		<HashRouter>
			<QueryClientProvider client={reactQueryClient}>
				<Header />
				<div className="flex h-screen overflow-hidden">
					<Sidebar />
					<main className="w-full pt-16">
						<ScrollArea>
							<Router />
						</ScrollArea>
					</main>
				</div>
				<Toaster />
				<ReactQueryDevtools initialIsOpen={false} />
			</QueryClientProvider>
		</HashRouter>
	);
};
