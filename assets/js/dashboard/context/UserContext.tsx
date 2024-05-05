import { useQuery, UseQueryResult } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import React, { createContext } from 'react';
import { NoPermission } from '../pages/403';
import { User } from './types';

const UserContext = createContext<UseQueryResult<User, Error> | null>(null);

const UserContextProvider = ({ children }: { children: React.ReactNode }) => {
	const userQuery = useQuery({
		queryKey: ['user'],
		queryFn: () =>
			apiFetch<User>({
				path: 'wp/v2/users/me?context=edit',
				method: 'GET',
			}),
		refetchInterval: 60000,
		refetchOnMount: true,
		refetchOnReconnect: true,
		refetchIntervalInBackground: true,
	});
	return (
		<UserContext.Provider value={userQuery}>
			{userQuery.isLoading ? (
				<div className="flex items-center justify-center w-full h-full">
					<div className="flex justify-center items-center space-x-1 text-sm text-gray-700">
						<svg
							fill="none"
							className="w-6 h-6 animate-spin"
							viewBox="0 0 32 32"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								clipRule="evenodd"
								d="M15.165 8.53a.5.5 0 01-.404.58A7 7 0 1023 16a.5.5 0 011 0 8 8 0 11-9.416-7.874.5.5 0 01.58.404z"
								fill="currentColor"
								fillRule="evenodd"
							/>
						</svg>
						<div>Loading ...</div>
					</div>
				</div>
			) : userQuery.data?.roles?.some((r) =>
					['administrator', 'hrhub_employee', 'hrhub_manager'].includes(r),
			  ) ? (
				children
			) : (
				<NoPermission />
			)}
		</UserContext.Provider>
	);
};

export { UserContext, UserContextProvider };
