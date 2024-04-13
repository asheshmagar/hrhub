import { useQuery, UseQueryResult } from '@tanstack/react-query';
import React, { createContext } from 'react';
import { Api } from '../../@/lib/api';
import { User } from './types';

declare var __HRHUB__: {
	userId?: string;
};

const UserContext = createContext<UseQueryResult<User, Error> | null>(null);

const UserContextProvider = ({ children }: { children: React.ReactNode }) => {
	const userApi = new Api('wp/v2/users');
	const userQuery = useQuery({
		queryKey: ['user'],
		queryFn: () =>
			userApi.get<User>(parseInt(__HRHUB__?.userId ?? '0'), 'edit'),
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
				<div>No Permission</div>
			)}
		</UserContext.Provider>
	);
};

export { UserContext, UserContextProvider };
