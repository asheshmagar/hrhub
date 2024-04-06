import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Plus } from 'lucide-react';
import React from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { buttonVariants } from '../../../@/components/ui/button';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { Api } from '../../../@/lib/api';
import { cn } from '../../../@/lib/utils';
import { EmployeesSchema } from '../../types/types';

export const Positions = () => {
	const api = new Api('hrhub/v1/department');
	const [searchParams, setSearchParams] = useSearchParams();
	const queryClient = useQueryClient();
	const positionsQuery = useQuery({
		queryKey: ['positions'],
		queryFn: () =>
			api.list<EmployeesSchema>({
				per_page: searchParams.get('limit') ?? undefined,
				page: searchParams.get('page') ?? undefined,
				search: searchParams.get('search') ?? undefined,
			}),
	});

	const employeesMutation = useMutation({
		mutationFn: (data: { per_page?: string; page?: string; search?: string }) =>
			api.list<EmployeesSchema>(data),
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
		employeesMutation.mutate({
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
		</div>
	);
};
