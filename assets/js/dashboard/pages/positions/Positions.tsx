import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Plus } from 'lucide-react';
import React from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { ListTable } from '../../../@/components/list-table';
import { buttonVariants } from '../../../@/components/ui/button';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { Api } from '../../../@/lib/api';
import { cn } from '../../../@/lib/utils';
import { PositionSchema } from '../../types/schema';
import { columns } from './components/columns';

type Position = {
	current: number;
	data: Array<PositionSchema & { id: number; employees: any[] }>;
	pages: number;
	total: number;
};

export const Positions = () => {
	const api = new Api('hrhub/v1/positions');
	const [searchParams, setSearchParams] = useSearchParams();
	const queryClient = useQueryClient();
	const positionsQuery = useQuery({
		queryKey: ['positions'],
		queryFn: () =>
			api.list<Position>({
				per_page: searchParams.get('limit') ?? undefined,
				page: searchParams.get('page') ?? undefined,
				search: searchParams.get('search') ?? undefined,
			}),
	});

	const positionsMutation = useMutation({
		mutationFn: (data: { per_page?: string; page?: string; search?: string }) =>
			api.list<Position>(data),
		onSuccess(data) {
			queryClient.setQueryData(['positions'], data);
		},
	});

	const onSearchParamsChange = (v: Record<string, string>) => {
		for (const key in v) {
			if (v[key] === '') {
				searchParams.delete(key);
			} else {
				searchParams.set(key, v[key]);
			}
		}
		setSearchParams(searchParams);
		positionsMutation.mutate({
			per_page: searchParams.get('limit') ?? undefined,
			page: searchParams.get('page') ?? undefined,
			search: searchParams.get('search') ?? undefined,
		});
	};

	return (
		<div className="flex-1 space-y-4  p-4 md:p-8 pt-6">
			<BreadCrumb items={[{ title: 'Positions', link: '/positions' }]} />
			<div className="flex items-start justify-between">
				<Heading
					title={`Positions (${positionsQuery?.data?.total ?? 0})`}
					description="Manage positions"
				/>

				<Link
					to={'/positions/new'}
					className={cn(buttonVariants({ variant: 'default' }))}
				>
					<Plus className="mr-2 h-4 w-4" /> Add New
				</Link>
			</div>
			<Separator />
			{positionsQuery.isLoading ? (
				<p>Loading...</p>
			) : (
				<ListTable
					columns={columns}
					data={
						positionsQuery.data?.data.map((e) => ({
							id: e.id,
							name: e.name,
							description: e.description ?? '(No description)',
							employees: e.employees?.length ?? 0,
						})) ?? []
					}
					pages={positionsQuery.data?.pages ?? -1}
					total={positionsQuery.data?.total ?? 0}
					onQuery={onSearchParamsChange}
					page={searchParams.get('page') ?? '1'}
					limit={searchParams.get('limit') ?? '10'}
					search={searchParams.get('search') ?? ''}
					loading={positionsQuery.isLoading || positionsMutation.isPending}
				/>
			)}
		</div>
	);
};
