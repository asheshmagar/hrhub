import { QueryClient } from '@tanstack/react-query';

export const reactQueryConfigs = {
	queries: {
		refetchOnWindowFocus: false,
		refetchOnReconnect: false,
		useErrorBoundary: true,
		retry: false,
	},
};

export const reactQueryClient = new QueryClient({
	defaultOptions: reactQueryConfigs,
});
